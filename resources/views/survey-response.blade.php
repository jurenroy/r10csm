@php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
@endphp

@php
// CC questions mapping
$ccAnswers = [
    1 => "Yes, aware before my transaction with this office",
    2 => "Yes, but aware only when I saw the CC of this office",
    3 => "No, not aware of the CC (Skip questions CC2 and CC3)",
];

$cc2Answers = [
    1 => "Yes, the CC was easy to find",
    2 => "Yes, but the CC was hard to find",
    3 => "No, I did not see this office's CC (Skip question CC3)",
];

$cc3Answers = [
    1 => "Yes, I was able to use the CC",
    2 => "No, I was not able to use the CC",
];

// SQD questions mapping (Likert scale)
$sqdAnswers = [
    1 => "Strongly Disagree",
    2 => "Disagree",
    3 => "Neither Agree nor Disagree",
    4 => "Agree",
    5 => "Strongly Agree",
];

// Region mapping
$regions = [
    'NCR' => 'National Capital Region',
    'CAR' => 'Cordillera Administrative Region',
    '1'   => 'Region 1 - Ilocos Region',
    '2'   => 'Region 2 - Cagayan Valley',
    '3'   => 'Region 3 - Central Luzon',
    '4A'  => 'Region 4A - Calabarzon',
    '4B'  => 'Region 4B - Mimaropa',
    '5'   => 'Region 5 - Bicol Region',
    '6'   => 'Region 6 - Western Visayas',
    '7'   => 'Region 7 - Central Visayas',
    '8'   => 'Region 8 - Eastern Visayas',
    '9'   => 'Region 9 - Zamboanga Peninsula',
    '10'  => 'Region 10 - Northern Mindanao',
    '11'  => 'Region 11 - Davao Region',
    '12'  => 'Region 12 - Soccksargen',
    '13'  => 'Region 13 - Caraga',
    'BARMM' => 'BARMM - Bangsamoro Autonomous Region in Muslim Mindanao',
];

// Client type mapping
$clientTypes = [
    1 => 'Citizen',
    2 => 'Business',
    3 => 'Government (Employee or another agency)',
];

// Service availed mapping (from Service model)
$service = \App\Models\Service::find($survey->service_id);
$serviceName = $service ? $service->service_code . ' - ' . $service->service_name : 'N/A';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Survey Response</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
    /* Body & Font */
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(145deg, #f5f7fa, #c3cfe2);
        margin: 0;
        padding: 40px 0;
        color: #333;
    }

    .response-summary {
        max-width: 800px;
        margin: auto;
        padding: 40px;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        border: 1px solid rgba(200,200,200,0.3);
        animation: fadeIn 1.5s ease-in-out;
    }

    h2 {
        text-align: center;
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        letter-spacing: 1px;
        margin-bottom: 10px;
    }

    h3 {
        text-align: center;
        font-size: 1.5rem;
        color: #34495e;
        margin-bottom: 30px;
    }

    ul {
        list-style: none;
        padding-left: 0;
    }

    li {
        background: #f9f9f9;
        margin-bottom: 15px;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    li:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .question {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        font-size: 1.05rem;
    }

    .answer {
        font-weight: 500;
        color: #34495e;
        font-size: 1rem;
        padding-left: 15px;
    }

    .qr-code {
        text-align: center;
        margin-top: 40px;
        padding: 20px;
        background: linear-gradient(135deg, #6dd5ed, #2193b0);
        border-radius: 20px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .qr-code h3 {
        color: #fff;
        margin-bottom: 15px;
    }

    .qr-code img {
        border: 5px solid #fff;
        border-radius: 15px;
    }
    .qr-code svg {
    border: 5px solid #fff;
    border-radius: 15px;
    padding: 10px; /* makes the white border bigger inside */
    background: #fff; /* ensures the border doesn’t merge with QR code black */
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

    /* Fade-in animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px);}
        to { opacity: 1; transform: translateY(0);}
    }

    /* Responsive */
    @media (max-width: 600px) {
        .response-summary {
            padding: 20px;
        }
        h2 { font-size: 2rem; }
        h3 { font-size: 1.2rem; }
        li { padding: 15px; }
    }
</style>
</head>
<body>
    <div class="response-summary">
        <h2>Thank You for Your Feedback!</h2>
        <h3>Your Response Summary:</h3>

        <ul>
            <li><span class="question">Age:</span> <span class="answer">{{ $survey->age }}</span></li>
            <li><span class="question">Sex:</span> <span class="answer">{{ $survey->sex == 1 ? 'Male' : 'Female' }}</span></li>
            <li><span class="question">Region of Residence:</span> <span class="answer">{{ $regions[$survey->region] ?? 'N/A' }}</span></li>
            <li><span class="question">Client Type:</span> <span class="answer">{{ $clientTypes[$survey->client_type] ?? 'N/A' }}</span></li>
            <li><span class="question">Agency Visited:</span> <span class="answer">{{ $survey->agency_visited }}</span></li>
            <li><span class="question">Service Availed:</span> <span class="answer">{{ $serviceName }}</span></li>

            <li><span class="question">CC1: Do you know about the Citizen's Charter?</span> 
                <span class="answer">{{ $ccAnswers[$survey->cc1] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">CC2: If Yes to the previous question, did you see this office's Citizen's Charter?</span> 
                <span class="answer">{{ $cc2Answers[$survey->cc2] ?? 'N/A'  }}</span>
            </li>
            <li><span class="question">CC3: If Yes to the previous question, did you use the Citizen's Charter as a guide?</span> 
                <span class="answer">{{ $cc3Answers[$survey->cc3] ?? 'N/A'  }}</span>
            </li>

            <li><span class="question">SQD1: I spent an acceptable amount of time to complete my transaction (Responsiveness)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd1] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD2: The office accurately informed and followed the transaction's requirements and steps (Reliability)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd2] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD3: My online transaction was simple and convenient (Access and Facilities)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd3] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD4: I easily found information about my transaction from the office or its website (Communication)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd4] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD5: I paid an acceptable amount of fees for my transaction (Costs)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd5] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD6: I am confident my online transaction was secure (Integrity)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd6] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD7: The office's online support was available / staff was helpful (Assurance)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd7] ?? 'N/A' }}</span>
            </li>
            <li><span class="question">SQD8: I got what I needed from the government office (Outcome)</span> 
                <span class="answer">{{ $sqdAnswers[$survey->sqd8] ?? 'N/A' }}</span>
            </li>

            <li><span class="question">Remarks:</span> <span class="answer">{{ $survey->remark ?? 'None'}}</span></li>
        </ul>

        <div class="qr-code">
            <h3>Scan QR code to view this response:</h3>
            {!! QrCode::size(200)->generate(route('survey.show_response', $survey->control_no)) !!}
        </div>
    </div>
</body>
</html>