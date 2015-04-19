<!doctype html>
<html lang="en">
<head>
  <title>OAuth CB</title>
</head>
<body>
  <table>
    <tr><td>Provider  </td><td><?php echo $userInfo['providername']; ?></td></tr>
    <tr><td>Identifier</td><td><?php echo $userInfo['identifier'  ]; ?></td></tr>
    <tr><td>User Name </td><td><?php echo $userInfo['nickname'    ]; ?></td></tr>
    <tr><td>Real Name </td><td><?php echo $userInfo['realname'    ]; ?></td></tr>
    <tr><td>Email     </td><td><?php echo $userInfo['email'       ]; ?></td></tr>
  </table>
  <a href="/">Home</a>
  <a href="/oauth/tokens/google"  >Google</a>
  <a href="/oauth/tokens/github"  >Github</a>
  <a href="/oauth/tokens/facebook">Facebook</a>
  <a href="/oauth/tokens/linkedin">LinkedIn</a>
  <a href="/oauth/tokens/twitter" >Twitter</a>
  <a href="/oauth/tokens/liveconnect">Live Connect</a>
  <script>
    window.opener.oauthCallback('<?php echo $oauthToken; ?>');
  </script>
</body>
</html>
