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
  <script>
    window.opener.oauthCallback('<?php echo $oauthToken; ?>');
  </script>
</body>
</html>
