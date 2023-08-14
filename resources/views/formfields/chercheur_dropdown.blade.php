
    <select name="chercheur_id">
        @foreach($supervisorOptions as $id => $name)
            <option value="{{ $id }}">{{ $name }}</option>
        @endforeach
    </select>