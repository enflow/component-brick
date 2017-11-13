<script>
    var brickReceiver = function (message, payload) {
        message = JSON.parse(message);
        payload = JSON.parse(payload);

        if (!message || !payload) {
            return {};
        }

        postData = {message: message, payload: payload, '_token': '{{ csrf_token() }}'};

        $.post('/brick/receiver', postData)
            .fail(function (xhr, status, error) {
                if (xhr.statusText == 'abort') {
                    return;
                }

                alert('Something went wrong: ' + xhr.responseText);
            });

        return postData;
    };

    @if ($message)
    brick.{{ $message->id() }}('{!! json_encode($message) !!}');
    @endif
</script>
