<table class="table table-hover text-center  custome-sm-table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Date</th>
            <th scope="col">Time In</th>
            <th scope="col">Time Out</th>
            <th scope="col">Working Hours</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($myAttendance as $key=> $row)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ format_date_two($row->date) }}</td>
                <td>{{ $row->in_time }}</td>
                <td>{{ $row->out_time }}</td>
                <td>{{ $row->working_hours }}</td>
            </tr>
        @empty
            <tr>
                <td class="text-center" colspan="5">No Data Found
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
