<table>
    <thead>
        <tr class="text-center">
            <th >No</th>
            <th >ID User Client</th>
            <th >ID Member</th>
            <th >Name</th>
            <th >Type Member</th>
            <th >Interval Month</th>
            <th >Start Date Member</th>
            <th >Exp Date Member</th>
            <th >Total Payment</th>
            <th >Email</th>
            <th >Telephone</th>
            <th >Date of Birth</th>
            <th >Address</th>
            <th >City</th>
            <th >Province</th>
            <th >IMG Profile</th>
            <th >IMG Member</th>
            <th >Created AT</th>
        </tr>
        </thead>
        <tbody>

        @php($i=1)

        @foreach($data as $d)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->id_user_client }}</td>
                <td>{{ $d->id_member }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->type_member }}</td>
                <td>{{ $d->interval_month }}</td>
                <td>{{ $d->start_member }}</td>
                <td>{{ $d->expied_member }}</td>
                <td>{{ $d->tot_payment }}</td>
                <td>{{ $d->email }}</td>
                <td>{{ $d->telephone }}</td>
                <td>{{ $d->date_of_birth }}</td>
                <td>{{ $d->address }}</td>
                <td>{{ $d->city }}</td>
                <td>{{ $d->province }}</td>
                <td>{{ $d->img_profile }}</td>
                <td>{{ $d->image_eMember }}</td>
                <td>{{ $d->created_at }}</td>
            </tr>
        @php($i++) 
             
        @endforeach
    </tbody>       
</table>