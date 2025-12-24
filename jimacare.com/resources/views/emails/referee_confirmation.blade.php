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
                            <h1 style="font-size: 24px; color: #333333;">Hello @if ($data['referee'] == 1)
                                    {{ $user->referee1_name }}
                                @else
                                    {{ $user->referee2_name }}
                                @endif
                            </h1>
                            <p style="font-size: 16px; color: #666666;"> {{ $user->firstname . ' ' . $user->lastname }}
                                has applied to us for employment and has provided your name as a referee.
                                We
                                would be grateful if you could complete the reference form by clicking below.</p>

                            <a href="{{ route('reference.confirm', ['id' => $user->id, 'reference' => $data['referee']]) }}">Proceed
                                to reference</a>
                            <p>The reference approval process is quick and easy. Please do not hesitate to contact us
                                with any questions, or click
                                <a
                                    href="{{ route('reference.cancel', ['id' => $user->id, 'reference' => $data['referee']]) }}">here</a>
                                if you would like to decline this request.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
