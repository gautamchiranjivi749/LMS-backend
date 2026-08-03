<!DOCTYPE html>
<html>
<head>
    <title>Certificate</title>

    <style>

        body{
            font-family: DejaVu Sans;
            text-align:center;
            border:15px solid #0d6efd;
            padding:60px;
        }

        h1{
            font-size:42px;
        }

        h2{
            color:#0d6efd;
        }

        p{
            font-size:18px;
        }

    </style>

</head>

<body>

<h1>Certificate of Completion</h1>

<p>This certificate is proudly presented to</p>

<h2>{{ $certificate->user->name }}</h2>

<p>for successfully completing the course</p>

<h2>{{ $certificate->course->title }}</h2>

<p>

Certificate No:
<strong>{{ $certificate->certificate_no }}</strong>

</p>

<p>

Issued:
{{ $certificate->issued_date->format('d M Y') }}

</p>

</body>
</html>