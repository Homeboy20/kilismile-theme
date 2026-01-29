<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Contacting Us</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .logo { width: 60px; height: 60px; margin: 0 auto 15px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .content { background: white; padding: 30px; border: 1px solid #ddd; border-top: none; }
        .message-summary { background: #f0f8f0; padding: 15px; border-left: 4px solid #4CAF50; margin: 20px 0; }
        .contact-info { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .contact-item { margin-bottom: 10px; }
        .contact-item i { color: #4CAF50; margin-right: 8px; width: 16px; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; font-size: 0.9rem; color: #666; border-radius: 0 0 8px 8px; }
        .btn { background: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block; margin: 10px 5px; }
        .btn:hover { background: #2E7D32; }
        .social-links { margin: 15px 0; }
        .social-links a { display: inline-block; margin: 0 10px; color: #4CAF50; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <span style="color: #4CAF50; font-size: 24px; font-weight: bold;">KS</span>
            </div>
            <h1>Thank You for Contacting Us!</h1>
            <p>We have received your message and will respond soon</p>
        </div>
        
        <div class="content">
            <p>Hello <strong>{{name}}</strong>,</p>
            
            <p>Thank you for reaching out to <strong>{{organization_name}}</strong>! We appreciate your interest in our work and mission to improve oral health in Tanzania.</p>
            
            <div class="message-summary">
                <h3>Your Message Summary:</h3>
                <p><strong>Subject:</strong> {{subject}}</p>
                <p><strong>Message:</strong></p>
                <p>{{message}}</p>
            </div>
            
            <p>We typically respond to all inquiries within <strong>24-48 hours</strong> during business days. Our team will review your message and get back to you as soon as possible.</p>
            
            <div class="contact-info">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <i>📧</i> <strong>Email:</strong> <a href="mailto:{{organization_email}}">{{organization_email}}</a>
                </div>
                <div class="contact-item">
                    <i>📞</i> <strong>Phone:</strong> {{organization_phone}}
                </div>
                <div class="contact-item">
                    <i>🌐</i> <strong>Website:</strong> <a href="{{website_url}}">{{website_url}}</a>
                </div>
            </div>
            
            <h3>While You Wait</h3>
            <p>Learn more about our work and how you can get involved:</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{website_url}}/programs" class="btn">Our Programs</a>
                <a href="{{website_url}}/volunteer" class="btn">Volunteer</a>
                <a href="{{website_url}}/donate" class="btn">Donate</a>
            </div>
            
            <div class="social-links">
                <p><strong>Follow Us:</strong></p>
                <a href="https://instagram.com/kili_smile">Instagram</a> |
                <a href="{{website_url}}/newsletter">Newsletter</a> |
                <a href="{{website_url}}/news">Latest News</a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Kilismile Organization</strong></p>
            <p>"No health without oral health"</p>
            <p>Improving oral health in Tanzania through education, prevention, and community outreach.</p>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 15px 0;">
            <p style="font-size: 0.8rem;">This is an automated response to confirm we received your message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>


