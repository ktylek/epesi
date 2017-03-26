{if $is_logged_in}

<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
  <span class="fa fa-user fa-3x"></span>&nbsp;{$logged_as}
  <span class=" fa fa-angle-down"></span>
</a>
<ul class="dropdown-menu dropdown-usermenu pull-right">
  <li>
    <a {$my_contact_href}>
      <span><i class="fa fa-user pull-right"></i>{"My Contact"|t}</span>
    </a>
  </li>
  {if $main_company_href}
  <li>
    <a {$main_company_href}>
      <span><i class="fa fa-building pull-right"></i>{"Main Company"|t}</span>
    </a>
  </li>
  {/if}
  <li>
    <a {$settings_href}>
      <span><i class="fa fa-cogs pull-right"></i>{"User Settings"|t}</span>
    </a>
  </li>
  <li><a {$logout_href}><i class="fa fa-sign-out pull-right"></i>{"Logout"|t}</a></li>
</ul>

{else}
<div id="login-screen" class="container">
    <div class="clearfix visible-md-block visible-lg-block" style="margin-top: 50px"></div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 panel panel-default">
            <div class="panel-body">
                <div class="logo img-responsive">{$logo}</div>
                {if $banned}
                    <p class="alert alert-danger">{'You have exceeded the number of allowed login attempts.'|t}</p>
                    <p><a href="{$epesi_url}">{'Host banned. Click here to refresh.'|t}</a></p>
                {else}

                    {$form_data.javascript}

                    <form {$form_data.attributes}>
                        {$form_data.hidden}
                        <!-- Display the fields -->

                        {if $is_demo}
                            <p><strong>EPESI DEMO APPLICATION</strong></p>
                        {/if}
                        {if isset($message)}
                            <div class="alert alert-info text-center">
                                {$message}
                            </div>
                            {if isset($message_action)}<p>{$message_action}</p>{/if}
                        {else}
                            {if $mode=='recover_pass'}
                                <p class="error">{$form_data.username.error}</p>
                                <p class="error">{$form_data.mail.error}</p>
                                <div class="form-group">
                                    <label>{$form_data.username.label}</label>
                                    {$form_data.username.html}
                                </div>
                                <div class="form-group">
                                    <label>{$form_data.mail.label}&nbsp;&nbsp;</label>
                                    {$form_data.mail.html}
                                </div>
                                {$form_data.submit_button.html}
                                <a {$back_href}><p>{"Cancel"|t}</p></a>
                            {else}
                                <p class="error">{$form_data.username.error}</p>
                                <p class="error">{$form_data.mail.error}</p>
                                <div class="form-group">
                                    <label>{$form_data.username.label}</label>
                                    {$form_data.username.html}
                                </div>
                                <div class="form-group">
                                    <label>{$form_data.password.label}</label>
                                    {$form_data.password.html}
                                </div>
                                {$form_data.submit_button.html}
                                <div class="checkbox">
                                    <label>{$form_data.autologin.html}</label>
                                    <small>{$form_data.warning.html}</small>
                                </div>
                            {/if}
                        {/if}
                    <p>{$form_data.recover_password.html}</p>
                    </form>
                {/if}
                {if isset($donation_note)}
                    <p>{$donation_note}</p>
                {/if}
                <!-- Epesi Terms of Use require line below - do not remove it! -->
                <p class="text-center">Copyright &copy; {php}echo date("Y"){/php} &bull; <a href="http://www.telaxus.com">Telaxus LLC</a> &bull; Managing Business Your Way<sup>TM</sup></p>
                <!-- Epesi Terms of Use require line above - do not remove it! -->

                <!-- Epesi Terms of Use require line below - do not remove it! -->
                <p class="text-center"><a href="http://epe.si/"><img src="images/epesi-powered.png" alt="EPESI powered" /></a></p>
                <!-- Epesi Terms of Use require line above - do not remove it! -->
            </p>
        </div>
    </div>
</div>
{/if}
