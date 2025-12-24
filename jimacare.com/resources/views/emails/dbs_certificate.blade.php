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
                            <h1 style="font-size: 24px; color: #333333;">DBS certificate of  {{ $user->firstname . ' ' . $user->lastname }}
                            </h1>
                            <p style="font-size: 16px; color: #666666;">Have DBS Certificate?</p>
                            <p style="font-size: 16px; color: #666666;"><b>Yes</b></p>

                            <p style="font-size: 16px; color: #666666; margin-top:5px">Type of DBS?</p>
                            <p style="font-size: 16px; color: #666666;"><b>{{$user->dbs_type}}</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Date of Issue?</p>
                            <p style="font-size: 16px; color: #666666;"><b>{{$user->dbs_issue}}</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Certificate number?</p>
                            <p style="font-size: 16px; color: #666666;"><b>{{$user->dbs_cert }}</b></p>



                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
