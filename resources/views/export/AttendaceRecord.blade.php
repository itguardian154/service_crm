<table>
    <thead>
        <tr class="text-center">
            <th >No</th>
            <th >ID</th>
            <th >ID User Client</th>
            <th >ID Member</th>
            <th >Member</th>
            <th >Tanggal</th>
            <th >Jam</th>
            <th >Name</th>
            <th >Email</th>
            <th >Telephone</th>
            <th >Date of Birth</th>
            <th >Address</th>
            <th >City</th>
            <th >Province</th>
            <th >Interval Month</th>
            <th >Date Start</th>
            <th >Date Expired</th>
        </tr>
        </thead>
        <tbody>

        @php($i=1)

        @foreach($data as $d)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->id_attendace }}</td>
                <td>{{ $d->id_user_client }}</td>
                <td>{{ $d->id_member }}</td>
                <td>{{ $d->type_member }}</td>
                <td>{{ $d->tanggal }}</td>
                <td>{{ $d->jam }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->email }}</td>
                <td>{{ $d->telephone }}</td>
                <td>{{ $d->date_of_birth }}</td>
                <td>{{ $d->address }}</td>
                <td>{{ $d->city }}</td>
                <td>{{ $d->province }}</td>
                <td>{{ $d->interval_month }}</td>
                <td>{{ $d->start_member }}</td>
                <td>{{ $d->expired_member }}</td>
            </tr>
        @php($i++) 
             
        @endforeach
    </tbody>       
</table>