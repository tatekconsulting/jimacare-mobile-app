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
                            <h1 style="font-size: 24px; color: #333333;">Hello Admin
                            </h1>
                            <p style="font-size: 16px; color: #666666;">
                                {{ $reference->first_name . ' ' . $reference->last_name }}
                                has confirmed himself as referee for {{ $user->firstname . ' ' . $user->lastname }}</p>
                            <h3 style="font-size: 24px; color: #333333;">Details Here</h3>
                            <p style="font-size: 16px; color: #666666;">Email:</p>
                            <p style="font-size: 16px; color: #666666;"><b>{{ $reference->email }}</b></p>

                            <p style="font-size: 16px; color: #666666; margin-top:5px">Job Title: </p>
                            <p style="font-size: 16px; color: #666666;"><b>{{ $reference->job_title }}</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Organisation:</p>
                            <p style="font-size: 16px; color: #666666;"><b>{{ $reference->organisation }}</b></p>
                            <h3 style="font-size: 16px; color: #666666;">User Details</h3>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Employment Duration number?</p>
                            <p style="font-size: 16px; color: #666666;"> <b>
                                    <?php
                                    $startDate = Carbon\Carbon::parse($reference->from);
                                    $endDate = Carbon\Carbon::parse($reference->to);
                                    $period = $startDate->diff($endDate);

                                    $years = $period->y;
                                    $months = $period->m;

                                    echo $years . ' years and ' . $months . ' months';
                                    ?></b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Employee Job Title: </p>
                            <p style="font-size: 16px; color: #666666;"><b>{{ $reference->emp_job_title }}</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Employee key duties: </p>
                            <p style="font-size: 16px; color: #666666;"><b>{{ $reference->emp_key_duty }}</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Comment: </p>
                            <p style="font-size: 16px; color: #666666;"><b>{{ $reference->comment }}</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Any issues regarding safety (previous disciplinary issues/dismissals) or
								competencies during their employment: </p>
                            <p style="font-size: 16px; color: #666666;"><b> @if ($reference->emp_safety_issue == true)
								Yes
							@else
								No
							@endif</b></p>
                            <p style="font-size: 16px; color: #666666; margin-top:5px">Would employ him/her again: </p>
                            <p style="font-size: 16px; color: #666666;"><b> @if ($reference->emp_again == true)
								Yes
							@else
								No
							@endif</b></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
