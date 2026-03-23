<table>
    <thead>
        <tr class="text-center">
            <th >No</th>
            <th >ID User Client</th>
            <th >Name</th>
            <th >Email</th>
            <th >Telephone</th>
            <th >Date of Birth</th>
            <th >Address</th>
            <th >City</th>
            <th >Province</th>
            <th >IMG Profile</th>
        </tr>
        </thead>
        <tbody>

        @php($i=1)

        @foreach($data as $d)
            <tr>
                <td>{{ $i }}</td>
                <td>{{ $d->id_user_client }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->email }}</td>
                <td>{{ $d->telephone }}</td>
                <td>{{ $d->date_of_birth }}</td>
                <td>{{ $d->address }}</td>
                <td>{{ $d->city }}</td>
                <td>{{ $d->province }}</td>
                <td>{{ $d->img_profile }}</td>
            </tr>
        @php($i++) 
             
        @endforeach
    </tbody>       
</table>