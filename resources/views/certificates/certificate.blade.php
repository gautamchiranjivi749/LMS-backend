<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>

    <style>
            @page {
            size: A4 landscape;
            margin: 0;
        }

        html,
        body {
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 40px;
            font-family: DejaVu Sans, sans-serif;
            background: #f5f7fb;
        }

        .certificate {
        width: 277mm;
        height: 190mm;
        margin: 10mm;
        border: 10px solid #1E3A8A;
        background: #ffffff;
        box-sizing: border-box;
        position: relative;
        overflow: hidden;
    }

       .inner-border {
    margin: 8mm;
    height: calc(100% - 16mm);
    border: 2px solid #60A5FA;
    padding: 18mm;
    box-sizing: border-box;
}
        .top-line {
            text-align: center;
            color: #2563EB;
            font-size: 18px;
            letter-spacing: 6px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        h1 {
            font-size:34pt;
    margin-bottom:8px;
        }

        .subtitle,
.description{
    font-size:13pt;
    line-height:1.8;
}

        .presented {
            text-align: center;
            color: #475569;
            font-size: 18px;
        }

       .student-name{
    font-size:28pt;
    color:#2563EB;
    font-weight:bold;
    margin:15px 0;
}

        .center {
            text-align: center;
        }

        .course {
            font-size: 28px;
            color: #0F172A;
            font-weight: bold;
            margin-top: 15px;
        }


        table {
            width: 100%;
            margin-top: 70px;
        }

        .label {
            color: #64748B;
            font-size: 14px;
            text-transform: uppercase;
        }

        .value {
            color: #0F172A;
            font-size: 18px;
            font-weight: bold;
            margin-top: 8px;
        }

        .signature {
            border-top: 2px solid #CBD5E1;
            width: 220px;
            padding-top: 10px;
            margin-top: 40px;
            text-align: center;
            color: #334155;
            font-size: 16px;
        }

        .seal {
            width: 95px;
            height: 95px;
            border: 4px solid #2563EB;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 15px;
            color: #2563EB;
            font-weight: bold;
            padding-top: 22px;
            margin: auto;
        }

       .footer{
    position:absolute;
    left:25mm;
    right:25mm;
    bottom:12mm;
    text-align:center;
    font-size:10pt;
}

        .brand {
            color: #2563EB;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 8px;
        }

        .gold-line {
            height: 4px;
            background: #F59E0B;
            margin: 25px auto;
            width: 180px;
        }
    </style>

</head>

<body>

<div class="certificate">

    <div class="inner-border">

        <div class="top-line">
            Learning Management System
        </div>

        <h1>Certificate of Completion</h1>

        <div class="gold-line"></div>

        <p class="subtitle">
            This certificate is proudly presented to
        </p>

        <div class="center">
            <div class="student-name">
                {{ $certificate->user->name }}
            </div>
        </div>

        <div class="description">
            For successfully completing the professional course
        </div>

        <div class="center">
            <div class="course">
                {{ $certificate->course->title }}
            </div>
        </div>

        <div class="description">
            Your dedication, perseverance, and commitment to learning have
            demonstrated outstanding achievement. We proudly recognize your
            successful completion of this course.
        </div>

        <table>

            <tr>

                <td width="35%">

                    <div class="label">
                        Certificate Number
                    </div>

                    <div class="value">
                        {{ $certificate->certificate_no }}
                    </div>

                </td>

                <td width="30%" align="center">

                    <div class="seal">
                        VERIFIED<br>LMS
                    </div>

                </td>

                <td width="35%" align="right">

                    <div class="label">
                        Issue Date
                    </div>

                    <div class="value">
                        {{ $certificate->issued_date->format('d M Y') }}
                    </div>

                </td>

            </tr>

        </table>

        <table style="margin-top:70px;">

            <tr>

                <td align="left">

                    <div class="signature">
                        Instructor Signature
                    </div>

                </td>

                <td align="right">

                    <div class="signature">
                        Director / LMS Authority
                    </div>

                </td>

            </tr>

        </table>

    </div>

    <div class="footer">

        <div class="brand">
            LMS • Learning Management System
        </div>

        Empowering learners through quality education and certified achievements.

    </div>

</div>

</body>

</html>