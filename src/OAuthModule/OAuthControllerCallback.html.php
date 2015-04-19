<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OAuth Callback</title>
  <base href="<?php echo $baseHref; ?>">
</head>
<body>
  <table>
    <tr><td>Provider  </td><td><?php echo $userInfo['providername']; ?></td></tr>
    <tr><td>Identifier</td><td><?php echo $userInfo['identifier'  ]; ?></td></tr>
    <tr><td>User Name </td><td><?php echo $userInfo['nickname'    ]; ?></td></tr>
    <tr><td>Real Name </td><td><?php echo $userInfo['realname'    ]; ?></td></tr>
    <tr><td>Email     </td><td><?php echo $userInfo['email'       ]; ?></td></tr>
  </table>
  <a href="">Home</a>
  <a href="oauth/tokens/google"  >Google</a>
  <a href="oauth/tokens/github"  >Github</a>
  <a href="oauth/tokens/facebook">Facebook</a>
  <a href="oauth/tokens/linkedin">LinkedIn</a>
  <a href="oauth/tokens/twitter" >Twitter</a>
  <a href="oauth/tokens/liveconnect">Live Connect</a>
  <script>
    window.opener.oauthCallback('<?php echo $oauthToken; ?>');
  </script>
</body>
</html>
