<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join TODO Application</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h1 style="color: #2563eb; margin-top: 0;">You're Invited!</h1>
        <p>You have been invited to join the TODO Application as an employee.</p>
    </div>

    <div style="background-color: #ffffff; padding: 20px; border: 1px solid #e5e7eb; border-radius: 5px; margin-bottom: 20px;">
        <p>Click the button below to accept the invitation and create your account:</p>
        <a href="{{ $acceptUrl }}" style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold;">
            Accept Invitation
        </a>
        <p style="margin-top: 20px; font-size: 12px; color: #6b7280;">
            Or copy and paste this link into your browser:<br>
            <span style="word-break: break-all;">{{ $acceptUrl }}</span>
        </p>
    </div>

    <div style="text-align: center; color: #6b7280; font-size: 12px; margin-top: 20px;">
        <p>This invitation link will expire once used.</p>
    </div>
</body>
</html>

