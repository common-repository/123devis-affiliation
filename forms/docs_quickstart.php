<div class="wrap">
<h2>Documentation</h2>
<h3>Quickstart</h3>

<p>The ServiceMagic WordPress plugin is designed to make it easy for you to embed ServiceMagic forms on your site and generate revenue. Follow these steps to get started fast. Start by clicking the ServiceMagic menu option (1).</p>
<img style="float:right" src="<?php print plugins_url('settings.png', __FILE__);?>">

<h3>First set the correct api location (2)</h3>

<p>Once you have the ServiceMagic plugin installed you will see the Settings page. The first thing to do is set the API location. Your Affiliate Rep will give you this information. </p>
<p>The Url will be <b>"https://local-api.servicemagic.eu"</b> (start with http and no trailing slash).</p>
<p>For the Server : if you are in France, choose <b>"FR"</b>, in the United Kingdom, choose <b>"UK"</b>.

<h3>Next, set your credentials(3)</h3>
<p>Your Affiliate Rep should have given you API credentials. Enter them here and click "Save Credentials". If you can't validate, first check to make sure your API Url is set to the correct locale. Otherwise contact your representative.</p>

<?php if (get_option('sm_creds')) { ?>

<br style="clear:both"/>
<hr/>
<h3>Make some Embeddable Forms</h3>

<img style="float:right" src="<?php print plugins_url('to_embeddable_form.png', __FILE__);?>">
<ol>
<li>Once you successfully set your credentials, the ServiceMagic EU plugin will have an expanded menu (1).</li>
<li>Click on the "Embeddable Forms" (2),</li>
<li>then the "Add new form" (3) to get to the form screen.</li>
</ol>
<p>Choose the layout you wish to display then click <b>"Save Form"</b>. You can choose between one step or two steps forms depending on your needs.</p>
<p>The <b>Form Name</b> is a label for you to track this form in the WordPress interface. It will appear on the embeddable forms list, in short-codes on post and pages, and the WYSIWYG short-code tool.</p>
<p>The <b>Tracking label</b> allows you to track the results of this form independently from other forms.</p>
<p>Next we'll find the form that you wish to display. First choose the category it resides in, then in the second drop down, choose the specific form. You can always revisit your selection by clicking "change". </p>
<p>The next step is to configure the appearance of the form. Choose first whether you wish to include required fields on the display. Choosing False will make your form slightly smaller.</p>
<p>Repeat the process if you wish to make multiple forms available. Or return to the Embeddable Forms to view the list of Embeddable Forms. </p>

<hr>

<img style="float:right"  src="<?php print plugins_url('shortcodes.png', __FILE__);?>"><h3>Embed the forms via short-codes into Posts or Pages</h3>
<ol>
<li>First identify the item you wish to add the form to. This can be either a Post or a Page on the main menu.</li>
<li>Once at the WYSIWYG form, click on the ServiceMagic Short-code generator (2).</li>
<li>This will open the list of available short codes, choose the one you wish (3).</li>
<li>This will result in inserting a short code into the WYSIWYG with the fields filled out.</li>
<li>You can now see your ServiceMagic form on your site by clicking on the View Post (5) button.</li>
</ol>
<p>If you are not satisfied with the appearance of the forms, see the "Form Defaults" menu option. </p>

<p style="font-weight:bold;color:red">Hey, Servicemagic forms in 5 minutes!</p>
<?php } ?>
</div>