<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JimaCare</title>

</head>

<body style="margin: 0; padding: 0; background-color: #f0f0f0;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f0f0f0">
        <tr>

            <td align="center">

                <table role="presentation" width="600" border="0" cellspacing="0" cellpadding="0"
                    bgcolor="#ffffff" style="border-radius: 8px; margin: 20px auto;">
                    <tr>
                        <td style="padding: 40px;">
                            <h1 style="font-size: 24px; color: #333333;">Hello {{$user->firstname.' '.$user->lastname}}
                            </h1>
                            <p style="font-size: 16px; color: #666666;">To complete your profile, complete the Care Training with training link below</p>

							<a href="https://buy.stripe.com/6oEaIG4OWdgbelidQZ">Join Training Now!</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
