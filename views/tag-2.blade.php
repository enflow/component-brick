{{-- Example: brickReceiver('{"id":"requestPushDeviceId"}', '{"name":"iPhone","deviceId":"b6682166-5070-4dc7-9ab3-05c20dd5c1e9"}') --}}

<script type="text/javascript">
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

        fetch('/brick/receiver', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(postData),
        })
            .then(function (response) {
                if (!response.ok) {
                    throw Error(response.statusText);
                }
                return response;
            })
            .catch((error) => {
                if (error.name === "AbortError") {
                    return;
                }

                console.error('Something went wrong: ' + error);
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

    document.addEventListener('click', e => {
        if (e.target.closest('.js-brick-file') && !e.bound) {
            e.preventDefault();

            e.bound = true;

            var element = e.target.closest('a');

            element.removeAttribute('target');
            element.removeAttribute('rel');

            @if ($brickManager->isAndroid())
            // Legacy versions of Brick don't support the 'openFile' JS action. We use a normal download for that.
            if (typeof brick.openFile === undefined) {
                return;
            }
            @endif

            var url = element.getAttribute('href');
            if (!url) {
                throw Error('No URL found for file.');
            }

            if (!/^[a-z][a-z0-9+.-]*:/.test(url)) {
                url = location.protocol + '//' + location.host + '/' + url.replace(/^\//g, '');
            }

            fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw Error(response.statusText);
                    }
                    return response;
                })
                .then((response) => response.json())
                .then(function (data) {
                    if (!data.url) {
                        throw Error('Unable to open file: URL unknown');
                    }

                    if (!data.filename) {
                        throw Error('Unable to open file: Filename unknown');
                    }

                    @if ($brickManager->isAndroid())
                    if (typeof brick === "undefined") {
                        throw Error('Unable to open file: brick bridge not setup');
                    }

                    brick.openFile(data.url, data.filename);
                    @else
                    if (typeof webkit === "undefined") {
                        throw Error('Unable to open file: webkit bridge not setup');
                    }

                    webkit.messageHandlers.openFile.postMessage({
                        'id': 'openFile',
                        'filename': data.filename,
                        'url': data.url
                    });
                    @endif
                })
                .catch((error) => {
                    if (error.name === "AbortError") {
                        return;
                    }

                    console.error(error);
                    alert('Unable to fetch file: ' + error);
                });
        }
    });

    @if ($brickManager->isIos())
    window.addEventListener('load', function () {
        var autofocus = document.querySelector('[autofocus]');
        if (autofocus) {
            window.scroll({
                top: autofocus.getBoundingClientRect().top + window.scrollY,
                behavior: 'smooth',
            });
        }
    }, false);
    @endif
</script>