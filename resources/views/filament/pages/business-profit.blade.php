<?php
$business_id = $id;
$business = App\Models\Investment::find($business_id);
$association_name = $business ? $business->entity->name : '';
if ($type)
    $transactions = App\Models\BusinessTransaction::where('business_id', $business_id)->where('type', $type)->get();
else
    $transactions = App\Models\BusinessTransaction::where('business_id', $business_id)->get();
?>

<!DOCTYPE html>
<html>

<head>
    <style>
        #my-table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #acacac;
        }

        #my-table td {
            border-collapse: collapse;
            border: 1px solid #acacac;
        }

        #my-table th,
        #my-table td {
            text-align: left;
            padding: 8px;
        }

        #my-table tr:nth-child(even) {
            background-color: #f2f2f2
        }

        #my-table th {
            background-color: #34a85d;
            color: white;
        }

        <?php if ($type == 'expense') { ?>#my-table th {
            background-color: #f43f5e;
        }
        <?php } ?>
        
        <?php if ($type == null) { ?>.filament-modal-actions {
            display: none;
        }
        <?php } ?>

        .new-button {
            border: 1px solid #acacac;
            padding: 7px 20px;
            text-decoration: none;
            cursor: pointer;
            border-radius: 12px;
            color: black;
        }

        .print-button{
            background-color: #34a85d;
            color: white;
        }

        #print-div {
            display: flex;
            justify-content: flex-end;
            margin: 0;
        }
    </style>
</head>

<body>
    @if(!$type)
    <div id="print-div">
        <a href="{{ url('/transactions/'.$business_id.'/print') }}" target="_blank" onclick="printPage(event)" class="new-button print-button">Print</a>
    </div>
    <h3 style="text-align: center; display:none;">Profit/Loss Calculation of {{ $association_name }}</h3>
    @endif
    <div>
        <table id="my-table">
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Title</th>
                <th>Code ID</th>
                <th>Transaction Date</th>
                <th>Amount</th>
            </tr>
            @foreach ($transactions as $transaction)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $transaction->type }}</td>
                <td>{{ $transaction->codeId ? $transaction->codeId->title : '-' }}</td>
                <td>{{ $transaction->codeId ? $transaction->codeId->code_id : '-' }}</td>
                <td>{{ $transaction->transaction_date ? $transaction->transaction_date : '-' }}</td>
                <td>{{ $transaction->amount?$transaction->amount:'-' }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="6"></td>
            </tr>
            @if($type == 'income')
            <tr>
                <th colspan="5">Total Income</th>
                <th>{{ $transactions->where('type', 'income')->sum('amount') }}</th>
            </tr>
            @endif
            @if($type == 'expense')
            <tr>
                <th colspan="5">Total Expense</th>
                <th>{{ $transactions->where('type', 'expense')->sum('amount') }}</th>
            </tr>
            @endif
            @if(!$type)
            <tr>
                <th colspan="5">Total Income</th>
                <th>{{ $transactions->where('type', 'income')->sum('amount') }}</th>
            </tr>
            <tr>
                <th colspan="5">Total Expense</th>
                <th>{{ $transactions->where('type', 'expense')->sum('amount') }}</th>
            </tr>
            <tr>
                <th colspan="5">Profit/Loss</th>
                <th>
                    @php
                    $profit_loss = $transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount');
                    if($profit_loss < 0) 
                        $result=$profit_loss . ' (Loss)'; 
                    else if ($profit_loss > 0)
                        $result=$profit_loss . ' (Profit)';
                    else
                        $result=$profit_loss;
                    @endphp 
                    {{ $result }} 
                </th>
            </tr>
            @endif
        </table>
    </div>
</body>

</html>

<script>
    function printPage(event) {
        event.preventDefault();
        event.target.style.display = "none";
        const h3 = document.querySelector('h3');
        if (h3) {
            h3.style.display = (getComputedStyle(h3).display === 'inline') ? 'inline-block' : 'block';
        }
        window.print();
    }
</script>