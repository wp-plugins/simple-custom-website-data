<h3 class="page-title">User Guide</h3>
<div class="cwd-container">
    <h3>When and Why Use CWD (Custom Website Data)</h3>
    <p>CWD allows you to simply store and retrive data quickly and easily for your own use. Example applications of this could be to save your websites contact email address and phone number. Storing them using CWD you can then output them using the simple shortcodes throughout your website; if something changes, no problem just update it in one place and you are good to go.</p>
    <p>Your custom data can either be displayed on the website using shortcodes or you can use the data to manipulate the website in ways only limited by your imagination!</p>
    <h3>Getting Started</h3>
    <p>Thank you for installing! Let's get you on the road to mastering this simple but powerful plugin.</p>
    <p>First things first, lets add some data. It's pretty simple really, just click the <a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management&view=add">Add New</a> button on the dashboard. Here you will enter the reference and the data. The "reference" is what uniquely identifies this record, the "data" is what you want to store and retrieve.</p>
    <p>Once you have entered your record and clicked submit you will now see a table on the dashboard displaying all your records. This table gives the reference, the shortcode needed to output the data you have stored and the data that is stored in the record.</p>
    <h3>Using the Shortcode</h3>
    <code>[cwd ref="$reference"]</code>
    <p>This plugin currently has one shortcode (shown above). This shortcode returns the data of the reference provided.  </p>
    <h4>Example</h4>
    <p>If you have a record with the reference "phone" which has the phone number "07383382938" putting the following shortcode <code>[cwd ref="phone"]</code> in a post or page will output 07383382938 on that page/post</p>
    <p>All shortcodes are generated on the plugin dashboard for your convenience.</p>
    <h3>Advanced Storage &amp; Use</h3>
    <p>CWD can be used in your websites theme to echo or perform php functions.</p>
    <h4>Using CWD Shortcode Outside Of Posts &amp; Pages</h4>
    <p>The built in Wordpress function <a href="http://codex.wordpress.org/Function_Reference/do_shortcode" target="_blank"><code>do_shortcode()</code></a> can be used to return the record's data</p>
    <p>
        Following on from the previous example; to output a record's data use:<br>
        <code>&lt;?php  echo do_shortcode('[cwd ref="phone"]') ?&gt;</code>
    </p>
    <h4>Storing An Array</h4>

    <p>To store an array use the following syntax:</p>
    <ul>
        <li>Seperate keys and values with the equals sign (=).</li>
        <li>Each item on a new line</li>
    </ul>
    <p>For example:</p>
    <code>
    key1=value1 <br> key2=value2
    </code>
    <h4>Retriveing data without shortcode</h4>
    <p>You can use a function to retrive your custom data, this is particulary useful when accessing an array as the shortcode will not work. This can be done by using the CWD php function below:<br>
    <code>&lt;?php  cwd_getThe($reference) ?&gt;</code><br>
    The "getThe" function can be used in place of <code>&lt;?php do_shortcode('[cwd ref="phone"]') ?&gt;</code> simply using <code>&lt;?php  cwd_getThe('phone') ?&gt;</code> will yield the same results.
    </p>
    <h4>Write to record using PHP</h4>
    <p>To update a record via PHP you can use the update function:</p>
    <code>&lt;?php cwd_updateThe($reference, $data) ?&gt;</code>
    <p><code>$reference</code> being the reference of the record you wish to change and <code>$data</code> being the data you want to be added to the record.</p>
    <p>Both of these parameters are required. <code>$data</code> can be one of the following formats:</p>
    <ul class="ul-list">
        <li>String</li>
        <li>Numeric</li>
        <li>Array</li>
    </ul>
    <p>The function will return true on success and false if the reference does not exist or the data type is not one of the above. </p>

    <br>
    <br>
    <a class="actionbtn" href="<?php echo site_url();?>/wp-admin/admin.php?page=cwd-management">Dashboard</a>
</div>