<!-- resources/views/reply-to-campaign.blade.php -->

<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répondre – {{ $senderName ?? "l'expéditeur" }} | SMART SMS</title>

    <!-- Tailwind CDN (à remplacer par votre build en prod) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js (obligatoire pour x-data, x-model, etc.) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Toastify -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

</head>
<body class="min-h-screen bg-gradient-to-b from-blue-50 to-white dark:from-gray-950 dark:to-gray-900 text-gray-900 dark:text-gray-100 antialiased">

    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 shadow-lg">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i data-lucide="message-square" class="w-7 h-7"></i>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold">Répondre à {{ $senderName ?? "l'expéditeur" }}</h1>
                    <p class="text-blue-100 text-sm mt-1">Votre réponse sera transmise directement</p>
                </div>
            </div>
            <a href="https://smartsms.gn" class="text-sm underline hover:text-blue-200 transition" target="_blank" rel="noopener noreferrer">
                SMART SMS
            </a>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="flex-1 flex items-center justify-center p-4 md:p-8">

        @if($invalid ?? false || !$tokenValid ?? false)
            <div class="max-w-md w-full text-center py-12">
                <i data-lucide="alert-circle" class="w-20 h-20 mx-auto text-red-500 mb-6"></i>
                <h1 class="text-2xl md:text-3xl font-bold mb-4">Lien invalide ou expiré</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">
                    Ce lien n'est plus valide ou la campagne associée n'existe plus.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Contactez l'expéditeur ou l'équipe support SMART SMS.
                </p>
            </div>

        @elseif(session("already_replied"))
            <div class="max-w-md w-full text-center py-12">
                <i data-lucide="info" class="w-20 h-20 mx-auto text-yellow-500 mb-6"></i>
                <h1 class="text-2xl md:text-3xl font-bold mb-4">Réponse déjà envoyée</h1>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Vous avez déjà répondu à ce message. Une seule réponse est acceptée par lien.
                </p>
            </div>

        @elseif(session("success"))
            <div class="w-full max-w-2xl space-y-6">

                {{-- Confirmation --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 md:p-10 border border-gray-200 dark:border-gray-700 text-center">
                    <div class="w-20 h-20 mx-auto bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="check-circle" class="w-12 h-12 text-green-600 dark:text-green-400"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-3">Réponse envoyée !</h2>
                    <p class="text-gray-600 dark:text-gray-300">
                        Votre message a bien été transmis à <strong>{{ session('senderName', $senderName ?? "l'expéditeur") }}</strong>.
                    </p>
                </div>

                {{-- CTA inscription --}}
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-xl p-8 md:p-10 text-white text-center">
                    <div class="w-16 h-16 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-5">
                        <i data-lucide="zap" class="w-9 h-9 text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Envoyez vous aussi des campagnes SMS</h3>
                    <p class="text-blue-100 mb-2 text-base">
                        Avec <strong>SMART SMS</strong>, communiquez avec vos clients, employés ou partenaires en quelques clics.
                    </p>
                    <ul class="text-blue-100 text-sm space-y-1 mb-7 inline-block text-left">
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-green-300"></i> Envoi en masse instantané</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-green-300"></i> Suivi des réponses en temps réel</li>
                        <li class="flex items-center gap-2"><i data-lucide="check" class="w-4 h-4 text-green-300"></i> Essai gratuit sans carte bancaire</li>
                    </ul>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="https://smartsms.gn/register"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold px-7 py-3 rounded-xl hover:bg-blue-50 transition shadow-md text-base">
                            <i data-lucide="user-plus" class="w-5 h-5"></i>
                            Créer un compte gratuit
                        </a>
                        <a href="https://smartsms.gn"
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 border border-white/40 text-white font-medium px-7 py-3 rounded-xl hover:bg-white/10 transition text-base">
                            En savoir plus
                        </a>
                    </div>
                </div>

            </div>

        @else
            <div class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 md:p-10 border border-gray-200 dark:border-gray-700">

                <form action="{{ route('reply.store', $token) }}" method="POST" class="space-y-7">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="response" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Votre message
                        </label>
                        <textarea
                            id="response"
                            name="content"
                            rows="8"
                            required
                            maxlength="1000"
                            placeholder="Écrivez votre réponse ici..."
                            class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-base text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 @error('content') border-red-500 @enderror"
                            x-data="{ chars: $el.value.length }"
                            x-init="
                                $watch('$el.value', val => chars = val.length);
                                $el.focus();
                            "
                            x-model="message"
                        ></textarea>

                        <div class="flex justify-between text-sm mt-2 text-gray-500 dark:text-gray-400">
                            <span x-text="chars + ' / 1000 caractères'"></span>
                            <span>Shift + Entrée pour saut de ligne</span>
                        </div>

                        @error('content')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($errors->any() && !$errors->has('content'))
                        <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-sm flex items-start gap-3">
                            <i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
                            <div>
                                <p class="font-medium">Veuillez corriger les erreurs suivantes :</p>
                                <ul class="list-disc pl-5 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <button
                        type="submit"
                        class="w-full py-4 px-8 rounded-xl font-bold text-lg transition-all flex items-center justify-center gap-3 shadow-lg
                            {{ $errors->any() ? 'bg-gray-400 dark:bg-gray-600 text-gray-700 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white' }}"
                        {{ $errors->any() ? 'disabled' : '' }}
                    >
                        @if($errors->any())
                            <i data-lucide="alert-circle" class="w-6 h-6"></i>
                            Corriger les erreurs
                        @else
                            <i data-lucide="send" class="w-6 h-6"></i>
                            Envoyer ma réponse
                        @endif
                    </button>
                </form>
            </div>
        @endif

        {{-- Signature branding de l'expéditeur --}}
        @if(!empty($branding))
            <div class="w-full max-w-2xl mt-6">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 py-5 flex items-center gap-4 shadow-sm"
                     style="border-left: 4px solid {{ $branding->primary_color ?? '#2563eb' }}">
                    @if($branding->logo)
                        <img src="{{ $branding->logo }}"
                             alt="{{ $branding->brand_name }}"
                             class="w-12 h-12 rounded-xl object-contain shrink-0 border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 p-1">
                    @else
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 text-white font-bold text-lg"
                             style="background-color: {{ $branding->primary_color ?? '#2563eb' }}">
                            {{ strtoupper(substr($branding->brand_name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-0.5">Message envoyé par</p>
                        <p class="font-bold text-gray-900 dark:text-white text-base leading-tight">{{ $branding->brand_name }}</p>
                        @if($branding->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $branding->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400 bg-white/70 dark:bg-gray-900/70 border-t border-gray-200 dark:border-gray-700 backdrop-blur-sm">
        <p>
            Propulsé par <strong class="text-blue-600 dark:text-blue-400">SMART SMS</strong> – Conakry, Guinée
        </p>
        <p class="mt-2">
            <a href="https://smartsms.gn" class="hover:text-blue-600 dark:hover:text-blue-400 transition" target="_blank" rel="noopener noreferrer">smartsms.gn</a>
            •
            <a href="tel:+22400000000" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Support</a>
        </p>
    </footer>

    <script>
        lucide.createIcons();

        // Toast pour succès / erreur (optionnel mais recommandé)
        @if(session("success"))
            Toastify({
                text: "Votre réponse a été envoyée avec succès !",
                duration: 6000,
                gravity: "top",
                position: "center",
                backgroundColor: "linear-gradient(to right, #10b981, #059669)",
                className: "rounded-xl shadow-2xl text-lg font-medium",
                offset: { y: 80 }
            }).showToast();
        @endif

        @if($errors->any())
            Toastify({
                text: "Veuillez corriger les erreurs affichées",
                duration: 5000,
                gravity: "top",
                position: "center",
                backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                className: "rounded-xl shadow-2xl text-lg font-medium",
                offset: { y: 80 }
            }).showToast();
        @endif
    </script>

</body>
</html>
