<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Training Application</title>
</head>
<body>
    <h3>New Training Application Received</h3>
    <p><strong>Name:</strong> {{ $name }}</p>
    <p><strong>Email:</strong> {{ $email }}</p>
    <p><strong>Organization:</strong> {{ $organization }}</p>
    <p><strong>Phone:</strong> {{ $phone }}</p>
    <p><strong>Position:</strong> {{ $position }}</p>
    <p><strong>Address:</strong> {{ $address ?? 'N/A' }}</p>
    <p><strong>Identity Card ID:</strong> {{ $identity_card_id }}</p>
    <p><strong>Training Name:</strong> {{ $trainingName }}</p>
    <p><strong>Message:</strong></p>
    <blockquote>Please review the application and contact the participant if necessary.</blockquote>
    <p>Best regards,</p>
    <p>The Zahin Oxus Team</p>
</body>
</html>
