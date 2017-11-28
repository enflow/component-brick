<script>
    // Example: brickReceiver('{"id":"requestPushDeviceId"}', '{"name":"iPhone van Michel","deviceId":"b6682166-5070-4dc7-9ab3-05c20dd5c1e9"}')
    var brickReceiver = function (message, payload) {
        message = JSON.parse(message);
        payload = JSON.parse(payload);

        if (!message || !payload) {
            return {};
        }

        var requestedMessage = '{{ $message ? $message->id() : '' }}';
        if (requestedMessage === '' || message.id !== requestedMessage) {
            alert('Didnt ask for message with payload ' + requestedMessage + '/' + payload);
            return;
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

    @if ($message && $brickManager->isAndroid())
    brick.{{ $message->id() }}('{!! json_encode($message) !!}');
    @endif
</script>
