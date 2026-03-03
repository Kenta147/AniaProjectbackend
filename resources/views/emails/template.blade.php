<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->subject }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; padding: 30px; text-align: center; }
        .header img { max-width: 100%; height: auto; border-radius: 4px; }
        .body { padding: 30px; color: #333333; line-height: 1.6; }
        .footer { background: #f0f0f0; padding: 20px; text-align: center; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        @if($template->image_url)
        <div class="header">
            <img src="{{ $template->image_url }}" alt="{{ $template->name }}">
        </div>
        @endif

        <div class="body">
            @if($subscriber->name)
            <p>Hola, <strong>{{ $subscriber->name }}</strong>.</p>
            @endif
            {!! $template->body !!}
        </div>

        <div class="footer">
            <p>Este email fue enviado a {{ $subscriber->email }}.</p>
            <p>Si no deseas recibir más correos, contáctanos.</p>
        </div>
    </div>
</body>
</html>
