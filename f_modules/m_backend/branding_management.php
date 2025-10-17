<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

// Initialize branding system
$branding = VBranding::getInstance();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!VSecurity::validateCSRFFromPost('branding_management')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $action = VSecurity::postParam('action', 'alphanum');
        
        switch ($action) {
            case 'update_settings':
                $updated = 0;
                $errors = [];
                
                // Get all posted settings
                foreach ($_POST as $key => $value) {
                    if (strpos($key, 'setting_') === 0) {
                        $settingKey = substr($key, 8); // Remove 'setting_' prefix
                        $settingType = VSecurity::postParam("type_$settingKey", 'alphanum', 'text');
                        
                        // Validate based on type
                        if ($settingType === 'color' && !preg_match('/^#[0-9A-Fa-f]{6}$/', $value)) {
                            $errors[] = "Invalid color format for $settingKey";
                            continue;
                        }
                        
                        if ($settingType === 'number' && !is_numeric($value)) {
                            $errors[] = "Invalid number format for $settingKey";
                            continue;
                        }
                        
                        if ($branding->set($settingKey, $value, $settingType)) {
                            $updated++;
                        }
                    }
                }
                
                if (empty($errors)) {
                    $success = "Successfully updated $updated branding settings.";
                } else {
                    $error = "Some settings could not be updated: " . implode(', ', $errors);
                }
                break;
                
            case 'apply_preset':
                $presetName = VSecurity::postParam('preset_name', 'text');
                if ($branding->applyPreset($presetName)) {
                    $success = "Successfully applied preset: $presetName";
                } else {
                    $error = "Failed to apply preset: $presetName";
                }
                break;
                
            case 'save_preset':
                $presetName = VSecurity::postParam('preset_name', 'text');
                $presetDescription = VSecurity::postParam('preset_description', 'text');
                $selectedSettings = VSecurity::postParam('selected_settings', 'text');
                
                if (empty($presetName)) {
                    $error = "Preset name is required.";
                } else {
                    // Get current settings for selected categories
                    $settings = [];
                    $categories = explode(',', $selectedSettings);
                    foreach ($categories as $category) {
                        $categorySettings = $branding->getByCategory(trim($category));
                        foreach ($categorySettings as $key => $data) {
                            $settings[$key] = $data['value'];
                        }
                    }
                    
                    if ($branding->savePreset($presetName, $presetDescription, $settings)) {
                        $success = "Successfully saved preset: $presetName";
                    } else {
                        $error = "Failed to save preset: $presetName";
                    }
                }
                break;
                
            case 'reset_category':
                $category = VSecurity::postParam('category', 'alphanum');
                $reset = 0;
                
                // Get default values for category
                $categorySettings = $branding->getByCategory($category);
                foreach ($categorySettings as $key => $data) {
                    if ($branding->set($key, $data['default'], $data['type'])) {
                        $reset++;
                    }
                }
                
                if ($reset > 0) {
                    $success = "Successfully reset $reset settings in $category category.";
                } else {
                    $error = "Failed to reset settings in $category category.";
                }
                break;
        }
    }
}

// Get current settings organized by category
$categories = $branding->getCategories();
$allSettings = [];
foreach ($categories as $category) {
    $allSettings[$category] = $branding->getByCategory($category);
}

// Get available presets
$presets = $branding->getPresets();

// Get site info for preview
$siteInfo = $branding->getSiteInfo();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branding Management - <?php echo htmlspecialchars($siteInfo['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .color-preview {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #ddd;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }
        .setting-group {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .preview-section {
            position: sticky;
            top: 20px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .badge-preview {
            margin: 2px;
        }
        .live-preview {
            border: 2px dashed #007bff;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            background: #f8f9fa;
        }
        .category-tabs .nav-link {
            border-radius: 8px 8px 0 0;
        }
        .category-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-palette"></i> Branding Management</h1>
                    <div>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#presetModal">
                            <i class="fas fa-magic"></i> Presets
                        </button>
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#savePresetModal">
                            <i class="fas fa-save"></i> Save Preset
                        </button>
                    </div>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" id="brandingForm">
                    <?php echo VSecurity::getCSRFField('branding_management'); ?>
                    <input type="hidden" name="action" value="update_settings">

                    <!-- Category Tabs -->
                    <ul class="nav nav-tabs category-tabs mb-4" id="categoryTabs">
                        <?php foreach ($categories as $index => $category): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                                   data-bs-toggle="tab" 
                                   href="#category-<?php echo $category; ?>">
                                    <i class="fas fa-<?php echo getCategoryIcon($category); ?>"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $category)); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Category Content -->
                    <div class="tab-content">
                        <?php foreach ($categories as $index => $category): ?>
                            <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" 
                                 id="category-<?php echo $category; ?>">
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h3><?php echo ucfirst(str_replace('_', ' ', $category)); ?> Settings</h3>
                                    <button type="button" class="btn btn-outline-warning btn-sm" 
                                            onclick="resetCategory('<?php echo $category; ?>')">
                                        <i class="fas fa-undo"></i> Reset to Defaults
                                    </button>
                                </div>

                                <div class="setting-group">
                                    <div class="row">
                                        <?php foreach ($allSettings[$category] as $key => $setting): ?>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">
                                                    <?php echo ucfirst(str_replace('_', ' ', str_replace($category . '_', '', $key))); ?>
                                                    <?php if (!empty($setting['description'])): ?>
                                                        <i class="fas fa-info-circle text-muted" 
                                                           title="<?php echo htmlspecialchars($setting['description']); ?>"></i>
                                                    <?php endif; ?>
                                                </label>
                                                
                                                <?php echo renderSettingInput($key, $setting); ?>
                                                
                                                <input type="hidden" name="type_<?php echo $key; ?>" value="<?php echo $setting['type']; ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="previewChanges()">
                            <i class="fas fa-eye"></i> Preview Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Preview Sidebar -->
            <div class="col-md-4">
                <div class="preview-section">
                    <h4><i class="fas fa-eye"></i> Live Preview</h4>
                    
                    <!-- Site Info Preview -->
                    <div class="live-preview">
                        <h5><?php echo htmlspecialchars($siteInfo['name']); ?></h5>
                        <p class="text-muted"><?php echo htmlspecialchars($siteInfo['tagline']); ?></p>
                        <p><small><?php echo htmlspecialchars($siteInfo['description']); ?></small></p>
                    </div>

                    <!-- Color Palette Preview -->
                    <div class="live-preview">
                        <h6>Color Palette</h6>
                        <div class="d-flex flex-wrap">
                            <?php
                            $colorSettings = $branding->getByCategory('colors');
                            foreach ($colorSettings as $key => $setting):
                                if ($setting['type'] === 'color'):
                            ?>
                                <div class="text-center me-2 mb-2">
                                    <div class="color-preview" style="background-color: <?php echo $setting['value']; ?>"></div>
                                    <small class="d-block"><?php echo substr($key, 6); ?></small>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>

                    <!-- Badge Preview -->
                    <div class="live-preview">
                        <h6>Badges</h6>
                        <?php echo $branding->getBadge('verified', null, 'badge-preview'); ?>
                        <?php echo $branding->getBadge('premium', null, 'badge-preview'); ?>
                        <?php echo $branding->getBadge('live', null, 'badge-preview'); ?>
                        <?php echo $branding->getBadge('new', null, 'badge-preview'); ?>
                    </div>

                    <!-- Button Preview -->
                    <div class="live-preview">
                        <h6>Buttons</h6>
                        <button class="btn btn-primary btn-sm me-1 mb-1">Primary</button>
                        <button class="btn btn-secondary btn-sm me-1 mb-1">Secondary</button>
                        <button class="btn btn-success btn-sm me-1 mb-1">Success</button>
                        <button class="btn btn-warning btn-sm me-1 mb-1">Warning</button>
                        <button class="btn btn-danger btn-sm me-1 mb-1">Danger</button>
                    </div>

                    <!-- Typography Preview -->
                    <div class="live-preview">
                        <h6>Typography</h6>
                        <h1 style="font-size: 24px;">Heading 1</h1>
                        <h2 style="font-size: 20px;">Heading 2</h2>
                        <p>Regular paragraph text with <a href="#">a link</a>.</p>
                        <small class="text-muted">Small muted text</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preset Selection Modal -->
    <div class="modal fade" id="presetModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-magic"></i> Apply Preset Theme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="presetForm">
                        <?php echo VSecurity::getCSRFField('branding_management'); ?>
                        <input type="hidden" name="action" value="apply_preset">
                        
                        <div class="row">
                            <?php foreach ($presets as $preset): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <?php echo htmlspecialchars($preset['name']); ?>
                                                <?php if ($preset['is_default']): ?>
                                                    <span class="badge bg-primary">Default</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="card-text"><?php echo htmlspecialchars($preset['description']); ?></p>
                                            <button type="submit" name="preset_name" value="<?php echo htmlspecialchars($preset['name']); ?>" 
                                                    class="btn btn-outline-primary btn-sm">
                                                Apply Theme
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Preset Modal -->
    <div class="modal fade" id="savePresetModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-save"></i> Save Current Settings as Preset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <?php echo VSecurity::getCSRFField('branding_management'); ?>
                    <input type="hidden" name="action" value="save_preset">
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Preset Name</label>
                            <input type="text" class="form-control" name="preset_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="preset_description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Include Categories</label>
                            <div class="form-check-group">
                                <?php foreach ($categories as $category): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="selected_categories[]" 
                                               value="<?php echo $category; ?>" id="cat_<?php echo $category; ?>" checked>
                                        <label class="form-check-label" for="cat_<?php echo $category; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $category)); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="selected_settings" id="selectedSettings">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Preset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update selected settings when checkboxes change
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_categories[]"]');
            const hiddenInput = document.getElementById('selectedSettings');
            
            function updateSelectedSettings() {
                const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                hiddenInput.value = selected.join(',');
            }
            
            checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedSettings));
            updateSelectedSettings(); // Initial call
        });

        function resetCategory(category) {
            if (confirm('Are you sure you want to reset all settings in the ' + category + ' category to their default values?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <?php echo VSecurity::getCSRFField('branding_management'); ?>
                    <input type="hidden" name="action" value="reset_category">
                    <input type="hidden" name="category" value="${category}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function previewChanges() {
            // This would open a new window/tab with the current settings applied
            window.open('/preview_branding.php', '_blank');
        }

        // Color input change handler for live preview
        document.addEventListener('input', function(e) {
            if (e.target.type === 'color') {
                // Update any color previews in real-time
                const colorPreviews = document.querySelectorAll('.color-preview');
                // Implementation would update preview colors
            }
        });
    </script>
</body>
</html>

<?php
function getCategoryIcon($category) {
    $icons = [
        'general' => 'cog',
        'logos' => 'image',
        'colors' => 'palette',
        'backgrounds' => 'fill',
        'text' => 'font',
        'borders' => 'square',
        'buttons' => 'hand-pointer',
        'typography' => 'text-height',
        'layout' => 'th-large',
        'badges' => 'award',
        'social' => 'share-alt',
        'advanced' => 'code',
        'player' => 'play-circle',
        'email' => 'envelope'
    ];
    
    return $icons[$category] ?? 'cog';
}

function renderSettingInput($key, $setting) {
    $value = htmlspecialchars($setting['value']);
    $inputName = "setting_$key";
    
    switch ($setting['type']) {
        case 'color':
            return "<div class=\"input-group\">
                        <span class=\"color-preview\" style=\"background-color: $value\"></span>
                        <input type=\"color\" class=\"form-control form-control-color\" name=\"$inputName\" value=\"$value\">
                    </div>";
            
        case 'boolean':
            $checked = $setting['value'] ? 'checked' : '';
            return "<div class=\"form-check form-switch\">
                        <input class=\"form-check-input\" type=\"checkbox\" name=\"$inputName\" value=\"1\" $checked>
                        <input type=\"hidden\" name=\"$inputName\" value=\"0\">
                    </div>";
            
        case 'number':
            return "<input type=\"number\" class=\"form-control\" name=\"$inputName\" value=\"$value\" step=\"0.1\">";
            
        case 'image':
            return "<div class=\"input-group\">
                        <input type=\"text\" class=\"form-control\" name=\"$inputName\" value=\"$value\" placeholder=\"Image URL or path\">
                        <button type=\"button\" class=\"btn btn-outline-secondary\" onclick=\"selectImage('$inputName')\">
                            <i class=\"fas fa-folder-open\"></i>
                        </button>
                    </div>";
            
        default:
            if (strlen($setting['value']) > 100) {
                return "<textarea class=\"form-control\" name=\"$inputName\" rows=\"3\">$value</textarea>";
            } else {
                return "<input type=\"text\" class=\"form-control\" name=\"$inputName\" value=\"$value\">";
            }
    }
}
?>