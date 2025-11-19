<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - PR Classes</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            text-align: center;
            padding: 50px 20px;
            margin: 0;
            line-height: 1.6;
        }
        .maintenance-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #007bff;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 30px;
        }
        .message {
            margin-bottom: 30px;
            font-size: 18px;
        }
        .timer {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .contact {
            font-size: 14px;
            color: #6c757d;
        }
        .contact a {
            color: #007bff;
            text-decoration: none;
        }
        .contact a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <img src="assets/images/logo.png" alt="PR Classes Logo" class="logo">
        <h1>Site Under Maintenance</h1>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <?php if ($end_time): ?>
        <div class="timer">
            Expected completion: <?php echo date('F j, Y, g:i a', $end_time); ?>
        </div>
        <?php endif; ?>
        <div class="contact">
            For urgent inquiries, please contact us at <a href="mailto:info@prclasses.in">info@prclasses.in</a> or <a href="https://wa.me/919042796696">WhatsApp</a>.
        </div>
    </div>
</body>
</html> 