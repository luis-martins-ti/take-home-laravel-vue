@php
    use Carbon\Carbon;
    Carbon::setLocale('pt_BR');
    $today = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
@endphp


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Solicitação de Exames</title>
</head>
<body style="font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 40px; position: relative;">

@foreach($groups as $group => $items)

    {{-- Cabeçalho --}}
    <div style="margin-bottom: 10px;">
        <img src="{{ public_path('images/logo.png') }}" style="width: 150px; float: left;">
        <div style="text-align: center; margin-top: 80px;">
            <span style="font-weight: bold; font-size: 16px;">Solicitação de Exames</span><br>
            <span style="font-weight: bold; font-size: 10px;">{{$group}}</span>
        </div>
        <div style="clear: both;"></div>
    </div>

    {{-- Dados do médico e paciente --}}
    <p><strong>Realizado por:</strong> {{ $doctor }}</p>
    <p><strong>Paciente:</strong> {{ $patient['name'] }}</p>
    <p><strong>CPF:</strong> {{ $patient['cpf'] }}</p>

    {{-- Tabela de exames --}}
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 6px; text-align: left;">Exame</th>
                <th style="border: 1px solid #000; padding: 6px; text-align: left; width: 60px;">Lat.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td style="border: 1px solid #000; padding: 6px;">
                        {{ $item->exam->name }}
                        @if ($item->comment)
                            <br><span style="font-size: 10px;"><strong>Obs.:</strong> {{ $item->comment }}</span>
                        @endif
                    </td>
                    <td style="border: 1px solid #000; padding: 6px;">{{ $item->laterality }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Observação por grupo --}}
    @if (!empty($groupObservations[$group]))
        <p style="margin-top: 10px;"><strong>Observação:</strong> {{ $groupObservations[$group] }}</p>
    @endif

    {{-- Assinatura e data --}}
    <div style="margin-top: 50px; text-align: center;">
        <p>São Paulo, {{ $today }}</p>
        <hr style="width: 50%;">
        <br>
        <p>
            {{ $doctor }}<br>
            CRM 2131231 / AB<br>
            RQE 123 / SP – Oftalmologia
        </p>
    </div>

    {{-- Rodapé fixo --}}
    <div style="position: absolute; bottom: 20px; width: 100%; text-align: center; font-size: 10px;">
        Rua Coronel Lisboa, 4200, Vila Mariana, CEP 04260-040, São Paulo - SP |
        Telefones: (11) 91111-1110 / (21) 99494-3940 / (47) 83046-8320
    </div>

    @if (!$loop->last)
        <div style="page-break-after: always;"></div>
    @endif

@endforeach

</body>
</html>
