<html>
<title>Chats</title>

<a href="https://dragonzap.com"><img src="{{asset('logo.png')}}" /></a>
<br />
<br />
<form action="{{route('chats.store')}}" method="POST">
    @csrf
    Select an assistant:
    <select name="assistant_codename">
        <option value="sally">Sally Assistant</option>
    </select>
    <input type="submit" name="submit" value="Create Chat" />
</form>

{{$conversations->count()}} conversations
<br />

@foreach($conversations as $conversation)
<a href="{{route('chats.view', $conversation)}}">{{$conversation->created_at}}</a> <br />
@endforeach