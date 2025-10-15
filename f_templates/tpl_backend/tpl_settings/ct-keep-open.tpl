		<div class="right-float right-padding10 font12" style="float: right;">
			<label>{lang_entry key="frontend.global.always.open"}</label>
			<span class="icheck-box-all"><input type="checkbox" {if $keep_entries_open eq "1"}checked="checked"{/if} name="keep_open" id="keep-open" value="1" /></span>
		</div>
		<script type="text/javascript">
		$('.icheck-box-all input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                        {rdelim});
                        $('input#keep-open').on('ifChecked', function(event) {ldelim} $("#all-open").click(); {rdelim});
                        $('input#keep-open').on('ifUnchecked', function(event) {ldelim} $("#all-close").click(); {rdelim});
                {rdelim});
                </script>
