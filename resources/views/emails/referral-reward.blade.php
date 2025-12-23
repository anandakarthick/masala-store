@php
    $businessName = \App\Models\Setting::get('business_name', 'Masala Store');
    $businessLogo = \App\Models\Setting::logo();
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Reward - {{ $businessName }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); padding: 30px; text-align: center;">
                @if($businessLogo)
                    <img src="{{ $businessLogo }}" alt="{{ $businessName }}" style="max-height: 50px; margin-bottom: 15px;">
                @endif
                <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🎉 Congratulations!</h1>
                <p style="color: #dcfce7; margin: 10px 0 0; font-size: 16px;">You've earned a referral reward!</p>
            </td>
        </tr>

        <!-- Reward Amount Box -->
        <tr>
            <td style="padding: 30px;">
                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px; padding: 25px; text-align: center; border: 2px solid #f59e0b;">
                    <p style="color: #92400e; font-size: 14px; margin: 0 0 5px;">Wallet Credit</p>
                    <p style="color: #78350f; font-size: 42px; font-weight: bold; margin: 0;">₹{{ number_format($rewardAmount, 2) }}</p>
                    <p style="color: #92400e; font-size: 14px; margin: 10px 0 0;">Added to your wallet!</p>
                </div>
            </td>
        </tr>

        <!-- Message -->
        <tr>
            <td style="padding: 0 30px 20px;">
                <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 0;">
                    Hi <strong>{{ $referrer->name }}</strong>,
                </p>
                <p style="color: #374151; font-size: 16px; line-height: 1.6; margin: 15px 0;">
                    Great news! Your friend <strong>{{ $referredUser->name }}</strong> just placed an order using your referral code, and you've earned a reward!
                </p>
            </td>
        </tr>

        <!-- Order Details -->
        <tr>
            <td style="padding: 0 30px 20px;">
                <div style="background-color: #f9fafb; border-radius: 8px; padding: 20px; border-left: 4px solid #16a34a;">
                    <h3 style="color: #111827; margin: 0 0 15px; font-size: 16px;">
                        <span style="margin-right: 8px;">📦</span>Order Details
                    </h3>
                    <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 14px;">
                        <tr>
                            <td style="color: #6b7280;">Order Number:</td>
                            <td style="color: #111827; font-weight: 600; text-align: right;">{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td style="color: #6b7280;">Order Amount:</td>
                            <td style="color: #111827; font-weight: 600; text-align: right;">₹{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #6b7280;">Referred By You:</td>
                            <td style="color: #111827; font-weight: 600; text-align: right;">{{ $referredUser->name }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>

        <!-- Wallet Balance -->
        <tr>
            <td style="padding: 0 30px 20px;">
                <div style="background-color: #ecfdf5; border-radius: 8px; padding: 20px; text-align: center; border: 1px solid #a7f3d0;">
                    <p style="color: #065f46; font-size: 14px; margin: 0;">Your Current Wallet Balance</p>
                    <p style="color: #047857; font-size: 28px; font-weight: bold; margin: 5px 0;">₹{{ number_format($referrer->wallet_balance, 2) }}</p>
                    <a href="{{ route('account.wallet') }}" style="display: inline-block; background-color: #16a34a; color: #ffffff; padding: 10px 25px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 10px;">
                        View Wallet
                    </a>
                </div>
            </td>
        </tr>

        <!-- Share More -->
        <tr>
            <td style="padding: 0 30px 30px;">
                <div style="background-color: #eff6ff; border-radius: 8px; padding: 20px; text-align: center; border: 1px solid #bfdbfe;">
                    <p style="color: #1e40af; font-size: 16px; font-weight: 600; margin: 0 0 10px;">
                        <span style="margin-right: 8px;">💰</span>Keep Earning!
                    </p>
                    <p style="color: #3b82f6; font-size: 14px; margin: 0 0 15px;">
                        Share your referral code with more friends and earn rewards on their orders!
                    </p>
                    <div style="background-color: #ffffff; border: 2px dashed #3b82f6; border-radius: 8px; padding: 15px; margin: 10px 0;">
                        <p style="color: #6b7280; font-size: 12px; margin: 0 0 5px;">Your Referral Code</p>
                        <p style="color: #1e40af; font-size: 24px; font-weight: bold; margin: 0; letter-spacing: 2px;">{{ $referrer->referral_code }}</p>
                    </div>
                    <a href="{{ $referrer->referral_link }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff; padding: 10px 25px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 10px;">
                        Share Referral Link
                    </a>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f3f4f6; padding: 25px 30px; text-align: center;">
                <p style="color: #6b7280; font-size: 14px; margin: 0 0 10px;">
                    Thank you for spreading the word about {{ $businessName }}!
                </p>
                <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                    © {{ date('Y') }} {{ $businessName }}. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
