<html>
<head></head>
<body>
    
    {t}Hello{/t} {$user.first_name} {$user.last_name},<br /><br />
    {t}Your password for accessing the Linnaeus NG administration has been reset.{/t}<br />
    {t}Your new password is:{/t} <b>{$new_password}</b><br />
    <a href="{$url}">{t}Go to the site to log in{/t}</a>.<br />
    {t}Sincerely,{/t}<br /><br />
    {t}The Linnaeus NG support team{/t}

</body>
</html>