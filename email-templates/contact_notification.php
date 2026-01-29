<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background: white; padding: 30px; border: 1px solid #ddd; }
        .field { margin-bottom: 15px; }
        .field label { font-weight: bold; color: #2E7D32; display: inline-block; width: 120px; }
        .field value { color: #333; }
        .message-content { background: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; margin: 15px 0; }
        .footer { background: #f5f5f5; padding: 15px; text-align: center; font-size: 0.9rem; color: #666; }
        .interest-badge { background: #e8f5e8; color: #2E7D32; padding: 4px 8px; border-radius: 12px; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
            <p>Someone has submitted a message through your website contact form</p>
        </div>
        
        <div class="content">
            <h2>Contact Details</h2>
            
            <div class="field">
                <label>Name:</label>
                <span class="value">{{name}}</span>
            </div>
            
            <div class="field">
                <label>Email:</label>
                <span class="value"><a href="mailto:{{email}}">{{email}}</a></span>
            </div>
            
            {{#phone}}
            <div class="field">
                <label>Phone:</label>
                <span class="value">{{phone}}</span>
            </div>
            {{/phone}}
            
            {{#organization}}
            <div class="field">
                <label>Organization:</label>
                <span class="value">{{organization}}</span>
            </div>
            {{/organization}}
            
            {{#interest}}
            <div class="field">
                <label>Interest Area:</label>
                <span class="interest-badge">{{interest}}</span>
            </div>
            {{/interest}}
            
            <div class="field">
                <label>Subject:</label>
                <span class="value">{{subject}}</span>
            </div>
            
            <div class="field">
                <label>Message:</label>
                <div class="message-content">
                    {{message}}
                </div>
            </div>
            
            <h3>Submission Information</h3>
            
            <div class="field">
                <label>Submitted:</label>
                <span class="value">{{submitted_time}}</span>
            </div>
            
            <div class="field">
                <label>IP Address:</label>
                <span class="value">{{ip_address}}</span>
            </div>
            
            <div class="field">
                <label>Website:</label>
                <span class="value"><a href="{{website_url}}">{{website_url}}</a></span>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Quick Actions:</strong></p>
            <p>
                <a href="mailto:{{email}}?subject=Re: {{subject}}&body=Hello {{name}},%0D%0A%0D%0AThank you for contacting us.%0D%0A%0D%0A" 
                   style="background: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; margin: 0 5px;">
                    Reply to {{name}}
                </a>
            </p>
            <p>This email was sent from the contact form on your website.</p>
        </div>
    </div>
</body>
</html>

