                <!-- Watermark Logo -->
            <div class="watermark-logo">
                <img src="{{ asset('img/logo.webp') }}" alt="Watermark" />
            </div>
            <style>
                .watermark-logo {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    opacity: 0.08;
                    z-index: 0;
                    width: 50%;
                    text-align: center;
                    pointer-events: none; /* ensures it doesn't interfere with form inputs */
                }
                
                .watermark-logo img {
                    max-width: 70%;
                    height: auto;
                }

                .card-body form {
                    position: relative;
                    z-index: 1; /* ensures form stays above watermark */
                }
            </style>