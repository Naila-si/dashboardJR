@extends('master')

@section('content')
<title>AI Chat - Powered by Gemini</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #e0f7fa, #ffffff);
      min-height: 100vh;
    }
    .ai-chat-wrapper {
      margin-top: 100px; /* JARAK supaya ga ketabrak menu atas */
      display: flex;
      justify-content: center;
      padding: 30px;
    }
    .chat-container {
      background: #fff;
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 720px;
    }
    h1 {
      text-align: center;
      font-size: 36px;
      font-weight: 700;
      margin-bottom: 25px;
      background: linear-gradient(45deg, #0077b6, #00b4d8);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    textarea {
      width: 100%;
      padding: 18px;
      border-radius: 14px;
      border: 1px solid #ccc;
      resize: vertical;
      min-height: 140px;
      font-size: 15px;
    }
    textarea:focus {
      border-color: #00b4d8;
      outline: none;
    }
    .form-footer {
      display: flex;
      justify-content: flex-end;
      margin-top: 20px;
    }
    button[type="submit"] {
      padding: 14px 28px;
      background: #00b4d8;
      color: white;
      border: none;
      border-radius: 14px;
      font-weight: 600;
      font-size: 15px;
      cursor: pointer;
    }
</style>

<div class="ai-chat-wrapper">
  <div class="chat-container">
    <h1> AI Chat</h1>

    @if (session('response'))
      <div class="response">
        <strong>Jawaban AI:</strong>
        <button class="copy-button" onclick="copyResponse()" title="Salin Jawaban">
      📋 Copy
    </button>
        <div id="response-text">{!! nl2br(e(session('response'))) !!}</div>
      </div>
    @endif

    <form action="{{ route('gemini.ask') }}" method="POST" id="ai-form">
      @csrf
      <textarea name="prompt" placeholder="Tulis pertanyaan, ide, atau minta kode..." required></textarea>
      <div class="form-footer">
        <button type="submit">Kirim</button>
      </div>
    </form>

    <div class="loading-indicator" id="loading" style="display:none;">Sedang memproses jawaban...</div>
  </div>
</div>

<script>
const form = document.getElementById('ai-form');
const loadingIndicator = document.getElementById('loading');

form.addEventListener('submit', function(event) {
  loadingIndicator.style.display = 'block';
  form.querySelector('button[type="submit"]').disabled = true;
});

function copyResponse() {
  const responseText = document.getElementById('response-text').innerText;
  navigator.clipboard.writeText(responseText).then(function() {
    Swal.fire({
      icon: 'success',
      title: 'Disalin!',
      text: 'Jawaban berhasil disalin ke clipboard.',
      timer: 1500,
      showConfirmButton: false
    });
  }, function(err) {
    Swal.fire({
      icon: 'error',
      title: 'Oops!',
      text: 'Gagal menyalin teks.',
    });
  });
}
</script>

@endsection
