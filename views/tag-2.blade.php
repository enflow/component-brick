{{-- Example: brickReceiver('{"id":"requestPushDeviceId"}', '{"name":"iPhone","deviceId":"b6682166-5070-4dc7-9ab3-05c20dd5c1e9"}') --}}

<script>
    var brickReceiver = function (message, payload) {
        message = JSON.parse(message);
        payload = JSON.parse(payload);

        if (!message || !payload) {
            return {};
        }

        var requestedMessage = '{{ $message ? $message->id() : '' }}';
        if (requestedMessage === '' || message.id !== requestedMessage) {
            return;
        }

        postData = {message: message, payload: payload, '_token': '{{ csrf_token() }}'};

        $.post('/brick/receiver', postData)
            .fail(function (xhr, status, error) {
                if (xhr.statusText === 'abort') {
                    return;
                }

                console.error('Something went wrong: ' + xhr.responseText);
            });

        return postData;
    };

    @if ($message)
    @if ($brickManager->isAndroid())
    brick.{{ $message->id() }}('{!! json_encode($message) !!}');
    @else
    if (typeof webkit !== "undefined") {
        webkit.messageHandlers.{{ $message->id() }}.postMessage({!! json_encode($message) !!});
    }
    @endif
    @endif

    @if (! $brickManager->isAndroid())
    $(function () {
        $(document).on('click', '.js-brick-file', function (e) {
            $(this).removeAttr('target');

            var url = $(this).attr('href');
            if (!/^[a-z][a-z0-9+.-]*:/.test(url)) {
                url = location.protocol + '//' + location.host + '/' + url.replace(/^\//g, '');
            }

            $.get(url, function (data) {
                if (!data.url) {
                    alert('Unable to open file: URL unknown');
                    return;
                }

                if (!data.filename) {
                    alert('Unable to open file: Filename unknown');
                    return;
                }

                if (typeof webkit === "undefined") {
                    alert('Unable to open file: webkit bridge not setup');
                    return;
                }

                webkit.messageHandlers.openFile.postMessage({
                    'id': 'openFile',
                    'filename': data.filename,
                    'url': data.url
                });
            }, 'json');

            return false;
        });

        var $autofocus = $(':input[autofocus]');
        if ($autofocus.length) {
            window.scrollTo(0, $autofocus.offset().top - 100);
        }
    });
    @endif
</script>
