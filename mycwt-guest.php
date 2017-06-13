<h2>Personal Details</h2>
<?php if($_GET["profile"] == true) { ?>
    <div class="confirmation edited">
        <h3>Profile edited successfully!</h3>
        <p>You can make more changes by clicking <a href="/my-cwt/edit-profile/" title="Edit My Profile">here</a>.</p>
    </div>
<?php } else if($_GET["preferences"] == true) { ?>
    <div class="confirmation edited">
        <h3>Email preferences edited successfully!</h3>
        <p>You can make more changes by clicking <a href="/my-cwt/email-preferences/" title="Edit My Email Preferences">here</a>.</p>
    </div>
<?php } else if($_GET["priority"] == true) { ?>
    <div class="confirmation edited">
        <h3>Priority emails purchased successfully!</h3>
        <p>You can edit your email preferences <a href="/my-cwt/email-preferences/" title="Edit My Email Preferences">here</a>.</p>
    </div>
<?php } else if($_GET["sent"] == true) { ?>
    <div class="confirmation edited">
        <h3>Your message has been sent!</h3>
        <p>It will be delivered to all users who have consented to receive direct messages in their Email Preferences.</p>
        <p>You can send another <a href="/my-cwt/send-message/" title="Send a Message">here</a>.</p>
    </div>
<?php } ?>
<div class="cols">
    <div class="colBox">
        <ul>
        <li><b>First Name:</b> <?php echo $current_user->user_firstname; ?></li>
        <li><b>Surname:</b> <?php echo $current_user->user_lastname; ?></li>
        <li><b>Username:</b> <?php echo $current_user->user_login; ?></li>
        <li><b>Email:</b> <?php echo $current_user->user_email; ?></li>
        </ul>
    </div>
</div>