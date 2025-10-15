            <div class="container cbp-spmenu-push vs-column">
                <div class="vs-column thirds">
                    <div class="live_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.l.p.c"}</div>
                        <i class="icon-live"></i>
                        <div class="timer-live">{if $live_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-live">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-live">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="live_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="vs-column thirds">
                    <div class="video_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.v.p.c"}</div>
                        <i class="icon-video"></i>
                        <div class="timer-videos">{if $video_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-video">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-video">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="video_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="vs-column thirds fit">
                    <div class="short_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.s.p.c"}</div>
                        <i class="icon-mobile"></i>
                        <div class="timer-shorts">{if $short_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-short">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-short">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="short_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                
                
                <div class="vs-column fourths">
                    <div class="image_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.i.p.c"}</div>
                        <i class="icon-image"></i>
                        <div class="timer-images">{if $image_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-image">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-image">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="image_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="vs-column fourths">
                    <div class="audio_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.a.p.c"}</div>
                        <i class="icon-headphones"></i>
                        <div class="timer-audios">{if $audio_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-audio">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-audio">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="audio_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="vs-column fourths">
                    <div class="document_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.d.p.c"}</div>
                        <i class="icon-file"></i>
                        <div class="timer-docs">{if $document_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-doc">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-doc">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="document_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="vs-column fourths fit">
                    <div class="blog_files files_holder">
                        <div class="counter_title">{lang_entry key="frontend.global.b.p.c"}</div>
                        <i class="icon-blog"></i>
                        <div class="timer-blogs">{if $blog_module eq "0"}{lang_entry key="backend.dashboard.stats.module"}{else}0{/if}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-blog">0</div>
                        <div class="status active-status">{lang_entry key="frontend.global.active"}</div>
                        <div class="small-inactive-timer small-inactive-timer-blog">0</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.inactive"}</div>
                        <div class="close_but icon-times" rel-close="blog_files"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="vs-column half">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-upload"></i>  {lang_entry key="backend.dashboard.file.this.week"}</h1>
                            {if $mod_total gt 1}
                            <div class="filters-loading">Loading filters...</div>
                            <div class="Titles-sub icheck-box no-display">
                            <div class="vs-column full">
                            <div class="filter-wrap">
                            {if $live_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="l" class="this-week-filter" checked="checked"><label><i class="icon-live"></i></label>
                            {/if}
                            {if $video_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="v" class="this-week-filter" checked="checked"><label><i class="icon-video"></i></label>
                            {/if}
                            {if $short_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="s" class="this-week-filter" checked="checked"><label><i class="icon-mobile"></i></label>
                            {/if}
                            {if $image_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="i" class="this-week-filter" checked="checked"><label><i class="icon-image"></i></label>
                            {/if}
                            {if $audio_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="a" class="this-week-filter" checked="checked"><label><i class="icon-headphones"></i></label>
                            {/if}
                            {if $document_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="d" class="this-week-filter" checked="checked"><label><i class="icon-file"></i></label>
                            {/if}
                            {if $blog_module eq "1"}
                                <input type="checkbox" name="this_week_filter" value="b" class="this-week-filter" checked="checked"><label><i class="icon-blog"></i></label>
                            {/if}
                            	<button class="button-grey search-button form-button save-button button-blue save-entry-button graph-update" id="this-week-uploads" type="button" value="1" onfocus="blur();">
			    		<span>{lang_entry key="frontend.global.apply"}</span>
			    	</button>
                            </div>
                            </div>
                            </div>
                            {/if}
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-1-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1-container"></ol>
                    </div>
                </div>
                    
                <div class="vs-column half fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-upload"></i> {lang_entry key="backend.dashboard.file.last.week"}</h1>
                            {if $mod_total gt 1}
                            <div class="filters-loading">Loading filters...</div>
                            <div class="Titles-sub icheck-box no-display">
                            <div class="vs-column full">
                            <div class="filter-wrap">
                            {if $live_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="l" class="last-week-filter" checked="checked"><label><i class="icon-live"></i></label>
                            {/if}
                            {if $video_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="v" class="last-week-filter" checked="checked"><label><i class="icon-video"></i></label>
                            {/if}
                            {if $short_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="s" class="last-week-filter" checked="checked"><label><i class="icon-mobile"></i></label>
                            {/if}
                            {if $image_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="i" class="last-week-filter" checked="checked"><label><i class="icon-image"></i></label>
                            {/if}
                            {if $audio_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="a" class="last-week-filter" checked="checked"><label><i class="icon-headphones"></i></label>
                            {/if}
                            {if $document_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="d" class="last-week-filter" checked="checked"><label><i class="icon-file"></i></label>
                            {/if}
                            {if $blog_module eq "1"}
                                <input type="checkbox" name="last_week_filter" value="b" class="last-week-filter" checked="checked"><label><i class="icon-blog"></i></label>
                            {/if}
                            	<button class="button-grey search-button form-button save-button button-blue save-entry-button graph-update" id="last-week-uploads" type="button" value="1" onfocus="blur();">
			    		<span>{lang_entry key="frontend.global.apply"}</span>
			    	</button>
                            </div>
                            </div>
                            </div>
                            {/if}
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-2-container"></figure>
                        <ol class="Chartjs-legend" id="legend-2-container"></ol>
                    </div>
                </div>
                <div class="clearfix"></div>
                
                <div class="vs-column half">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-users"></i> {lang_entry key="backend.dashboard.members.week"}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <figure class="Chartjs-figure" id="chart-4-container"></figure>
                        <ol class="Chartjs-legend" id="legend-4-container"></ol>
                    </div>
                </div>
                
                <div class="vs-column half fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="iconBe-coin"></i> {lang_entry key="backend.dashboard.earn.year"}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <figure class="Chartjs-figure" id="chart-8-container"></figure>
                        <ol class="Chartjs-legend" id="legend-8-container"></ol>
                    </div>
                </div>

		<div id="dash-dn">
                <div class="vs-column thirds stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-live"></i> {lang_entry key="backend.dashboard.stats.l"} {if $lcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-11-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-11-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-11a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-11a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column thirds stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main">
                            	<i class="icon-video"></i> {lang_entry key="backend.dashboard.stats.v"}
                            	{if $vcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}
                            </h1>
                            <div class="Titles-sub"></div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-3-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-3-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-3a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-3a-container"></ol>
                        </div>
                    </div>
                </div>
                
                <div class="vs-column thirds stats fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main">
                                <i class="icon-mobile"></i> {lang_entry key="backend.dashboard.stats.s"}
                                {if $scount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}
                            </h1>
                            <div class="Titles-sub"></div>
                        </header>
                        <div class="vs-column half">
                            <figure class="Chartjs-figure" id="chart-12-container"></figure>
                            <ol class="Chartjs-legend" id="legend-12-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                            <figure class="Chartjs-figure" id="chart-12a-container"></figure>
                            <ol class="Chartjs-legend" id="legend-12a-container"></ol>
                        </div>
                    </div>
                </div>
                
                <div class="clearfix"></div>

                <div class="vs-column fourths stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-image"></i> {lang_entry key="backend.dashboard.stats.i"} {if $icount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-6-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-6-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-6a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-6a-container"></ol>
                        </div>
                    </div>
                </div>
                <div class="vs-column fourths stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-headphones"></i> {lang_entry key="backend.dashboard.stats.a"} {if $acount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-5-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-5-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-5a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-5a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column fourths stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-file"></i> {lang_entry key="backend.dashboard.stats.d"} {if $dcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-7-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-7-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-7a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-7a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column fourths fit stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-blog"></i> {lang_entry key="backend.dashboard.stats.b"} {if $bcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-9-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-9-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-9a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-9a-container"></ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            </div>