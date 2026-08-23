<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .header-section {
            background-color: #dc3545;
            color: white;
            padding: 3rem 0;
            text-align: center;
        }
        .content-section {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-weight: 700;
        }
        h2 {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        p, li {
            line-height: 1.8;
            color: #333;
            font-weight: 400;
            font-size: 1rem;
        }
        ul {
            padding-left: 20px;
        }
        .contact-section {
            text-align: center;
            margin-top: 2rem;
        }
        .contact-section a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 500;
        }
        .contact-section a:hover {
            text-decoration: underline;
        }
        footer {
            background-color: #dc3545;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
            font-weight: 400;
        }
    </style>
@extends('layouts.landing.app')
@section('privacy-policy','active')
@section('title',translate('messages.privacy_policy'))

@section('content')

        <!-- Page Header Gap -->
        <div class="h-148px"></div>
        <!-- Page Header Gap -->

        <!-- ======= Privacy Section ======= -->
        <section class="privacy-section">
            <div class="container">
                <div class="section-wrapper">
                    <div class="section-wrapper-inner">
                        <div class="section-header mw-100">
                            <h2 class="title"> <span class="text-base">{{translate('messages.privacy_policy')}}</span></h2>
                        </div>
                        <div class="about--content">
                            <!--{!! $data !!}-->
                            
                             <!-- Header Section -->
    <div class="header-section">
        <h1>Spot2Delivery Privacy Policy</h1>
        <p>Effective Date: May 10, 2025</p>
    </div>

    <!-- Content Section -->
    <div class="content-section">
        <h2>1. Introduction</h2>
        <p>Spot2Delivery ("we," "us," or "our") operates a food delivery platform comprising three applications: User App, Restaurant App, and Delivery Man App (collectively, "the Apps"). We are committed to protecting your privacy and ensuring transparency about how your personal information is handled. This Privacy Policy explains how we collect, use, share, and protect your information in compliance with applicable laws, including the General Data Protection Regulation (GDPR), California Consumer Privacy Act (CCPA), and Google Play policies.</p>

        <h2>2. Information We Collect</h2>
        <p>We collect the following types of information to provide and improve our services:</p>
        <ul>
            <li><strong>Personal Information:</strong> When you register or use the Apps, we may collect your name, email address, phone number, delivery address, and payment information (e.g., credit/debit card details processed via secure third-party payment gateways).</li>
            <li><strong>Restaurant App Data:</strong> For restaurant partners, we collect business details (e.g., restaurant name, address), menu information, and order history.</li>
            <li><strong>Delivery Man App Data:</strong> For delivery personnel, we collect identification details (e.g., name, ID documents), contact information, bank account details for payments, and delivery history.</li>
            <li><strong>Location Data:</strong> With your consent, we collect real-time location data to enable order tracking, assign deliveries, and optimize routes. You can disable location access, but this may limit functionality.</li>
            <li><strong>Device and Usage Data:</strong> We collect information about your device (e.g., IP address, device type, operating system) and how you interact with the Apps (e.g., pages visited, features used).</li>
            <li><strong>Communications:</strong> Messages or feedback you send through the Apps or to our support team.</li>
        </ul>

        <h2>3. How We Use Your Information</h2>
        <p>We use your information for the following purposes:</p>
        <ul>
            <li>To process and fulfill food orders, including matching users with restaurants and delivery personnel.</li>
            <li>To enable communication between users, restaurants, and delivery personnel (e.g., order updates, delivery status).</li>
            <li>To improve and personalize the Apps, such as recommending restaurants or optimizing delivery routes.</li>
            <li>To process payments securely through third-party payment providers.</li>
            <li>To send promotional offers, newsletters, or updates (you can opt out at any time).</li>
            <li>To analyze usage trends and enhance app performance.</li>
            <li>To comply with legal obligations and prevent fraud or misuse of the Apps.</li>
        </ul>

        <h2>4. Sharing Your Information</h2>
        <p>We may share your information with:</p>
        <ul>
            <li><strong>Restaurants:</strong> To process and fulfill your orders (e.g., sharing your delivery address and order details).</li>
            <li><strong>Delivery Personnel:</strong> To facilitate deliveries (e.g., sharing your name, address, and contact details).</li>
            <li><strong>Third-Party Service Providers:</strong> We work with trusted partners for payment processing, cloud storage, analytics, and customer support. These providers are contractually obligated to protect your data.</li>
            <li><strong>Legal Authorities:</strong> When required by law, such as in response to a subpoena, court order, or to protect our rights, safety, or property.</li>
            <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred to a successor entity.</li>
        </ul>
        <p>We do not sell your personal information to third parties.</p>

        <h2>5. Data Security</h2>
        <p>We implement industry-standard security measures, including encryption, secure sockets layer (SSL) technology, and access controls, to protect your information. However, no method of transmission over the internet or electronic storage is completely secure, and we cannot guarantee absolute security.</p>

        <h2>6. Your Rights and Choices</h2>
        <p>Depending on your location, you may have the following rights regarding your personal information:</p>
        <ul>
            <li><strong>Access:</strong> Request a copy of the personal information we hold about you.</li>
            <li><strong>Correction:</strong> Update or correct inaccurate information.</li>
            <li><strong>Deletion:</strong> Request deletion of your personal information, subject to legal obligations.</li>
            <li><strong>Opt-Out:</strong> Unsubscribe from marketing communications or opt out of personalized ads.</li>
            <li><strong>Data Portability:</strong> Request a copy of your data in a structured, machine-readable format.</li>
            <li><strong>Restrict Processing:</strong> Limit how we process your data in certain circumstances.</li>
        </ul>
        <p>To exercise these rights, contact us at <a href="mailto:spot2delivery@gmail.com">spot2delivery@gmail.com</a>. You can also manage settings in the Apps, such as disabling location tracking or updating your profile.</p>

        <h2>7. Children's Privacy</h2>
        <p>The Apps are not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13. If we learn that we have collected such information, we will take steps to delete it. If you believe a child under 13 has provided us with personal information, please contact us.</p>

        <h2>8. International Data Transfers</h2>
        <p>Your information may be transferred to and processed in countries other than your own, including countries with different data protection laws. We ensure appropriate safeguards, such as standard contractual clauses, are in place to protect your data during such transfers.</p>

        <h2>9. Data Retention</h2>
        <p>We retain your personal information only for as long as necessary to fulfill the purposes outlined in this Privacy Policy or to comply with legal obligations. For example, we may retain order history for accounting purposes or delete inactive accounts after a specified period.</p>

        <h2>10. Third-Party Links</h2>
        <p>The Apps may contain links to third-party websites or services (e.g., payment gateways). We are not responsible for the privacy practices of these third parties. We encourage you to review their privacy policies.</p>

        <h2>11. Changes to This Privacy Policy</h2>
        <p>We may update this Privacy Policy to reflect changes in our practices or legal requirements. We will notify you of material changes by posting the updated policy on this page with a revised "Effective Date" or through in-app notifications. Please review this policy periodically.</p>
        
        <h2>13. Account and Data Deletion</h2> <p>This section explains how users of the Spot2Delivery app can request deletion of their account and associated data.</p> <h3>How to Request Deletion</h3> <p>To request deletion of your Spot2Delivery account and associated data, please send an email to <strong>support@spot2delivery.com</strong> with the subject line <em>“Account/Data Deletion Request.”</em> In your email, include:</p> <ul> <li>Your username, registered email, or phone number used in the app</li> <li>Whether you'd like to delete your account, your data, or both</li> <li>A brief reason (optional)</li> </ul> <h3>What Will Be Deleted</h3> <p>Upon verification, we will delete the following:</p> <ul> <li>Your account credentials (username, email, phone number)</li> <li>Any personal data you provided (such as delivery addresses and contact preferences)</li> <li>Associated usage data and history</li> </ul> <h3>What May Be Retained</h3> <p>Some data may be retained for legal, regulatory, or fraud prevention purposes for up to 30 days after deletion, in accordance with our data retention policy.</p> <h3>Developer Contact</h3> <p>Developer: Spot2Delivery<br> Email: <strong>support@spot2delivery.com</strong></p>

        <h2>12. Contact Us</h2>
        <div class="contact-section">
            <p>If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us at:</p>
            <p>Email: <a href="mailto:spot2delivery@gmail.com">spot2delivery@gmail.com</a></p>
            <p>We aim to respond to all inquiries within 30 days.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Spot2Delivery. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ======= Privacy Section ======= -->
@endsection
