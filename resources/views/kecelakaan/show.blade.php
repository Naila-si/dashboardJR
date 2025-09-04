@extends('sidebar')

@section('title', 'Rencana Penanganan Kecelakaan')

@section('content')
<div class="p-6 bg-white shadow rounded-md">

    <!-- Header lokasi & informasi umum -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Rencana Penanganan Kecelakaan</h1>
        <p class="text-gray-500">{{ $data->lokasi }}</p>

        <div class="flex gap-6 mt-4">
            <div>
                <p class="text-sm text-gray-500">Tahun</p>
                <p class="font-semibold">{{ $data->tahun ?? '2024' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Korban MD</p>
                <p class="font-semibold">{{ $data->korban_md ?? 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Korban LL</p>
                <p class="font-semibold">{{ $data->korban_ll ?? 0 }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Korban</p>
                <p class="font-semibold">{{ $data->total_korban ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Bagian Activity & Kanban -->
    <div class="flex gap-6 w-full">

        <!-- Kolom kiri: Activity (2/3 lebar) -->
        <div class="flex-1 space-y-4">
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('kecelakaan.index') }}"
                class="text-red-700 hover:underline flex items-center gap-1">
                ← Kembali
                </a>
                <button onclick="openActivityModal()"
                    class="bg-red-700 text-white px-3 py-1 rounded-md text-sm">
                    + Tambah Activity
                </button>
            </div>

            <!-- List Activity -->
            <div id="activity-list" class="space-y-4">
                <!-- Activity dari database -->
                @foreach($data->activities as $activity)
                <div class="border rounded-md p-4" data-id="{{ $activity->id }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-semibold text-red-700">{{ $activity->title }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $activity->description }}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openActivityModal(this)"
                            class="bg-orange-200 text-red-700 px-2 py-1 rounded-md text-xs">Edit</button>
                            <form action="{{ route('activity.destroy', [$data, $activity]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-200 text-red-700 px-2 py-1 rounded-md text-xs">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
        </div>

        <!-- Kolom kanan: Kanban (1/3 lebar) -->
        <div class="flex-1 flex gap-4">
            <!-- In Progress -->
            <div class="mb-4">
                <h2 class="bg-yellow-400 text-white font-semibold px-3 py-1 rounded-md inline-block mb-2">
                    In Progress
                </h2>
                <div class="space-y-2 w-full">
                    <div class="flex items-center justify-between border rounded-md px-3 py-2">
                        <span>Identifikasi Lokasi</span>
                        <input type="checkbox" checked>
                    </div>
                </div>
            </div>

            <!-- To do -->
            <div class="flex-1">
                <h2 class="bg-red-700 text-white font-semibold px-3 py-1 rounded-md inline-block mb-2">
                    To Do
                </h2>
                <div class="space-y-2 w-full">
                    <div class="flex items-center justify-between border rounded-md px-3 py-2">
                        <span>Identifikasi Lokasi</span>
                        <input type="checkbox">
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Modal Tambah/Edit Activity -->
<div id="activityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-md shadow-md w-[90%] max-w-[1200px]">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold" id="modalTitle">Add List</h2>
            <button type="button" onclick="closeActivityModal()" class="text-sm text-red-700">Go Back</button>
        </div>

        <form id="activityForm" action="{{ route('activity.store', $data) }}" method="POST" enctype="multipart/form-data" onsubmit="saveActivity(event)">
            @csrf
            <input type="hidden" name="activity_id" id="activityId">
            <!-- Title -->
            <div class="mb-3">
                <label class="block text-sm font-medium">Title</label>
                <input type="text" id="activityTitle" name="title"
                    class="w-full border rounded-md px-2 py-1" required>
            </div>

            <!-- Date -->
            <div class="mb-3">
                <label class="block text-sm font-medium">Date</label>
                <input type="date" id="activityDate" name="date"
                    class="w-full border rounded-md px-2 py-1">
            </div>

            <!-- Priority -->
            <div class="mb-3">
                <label class="block text-sm font-medium">Priority</label>
                <div class="flex gap-6 mt-1">
                    <label class="flex items-center gap-1 text-red-600">
                        <input type="radio" name="priority" value="Extreme"> Extreme
                    </label>
                    <label class="flex items-center gap-1 text-blue-600">
                        <input type="radio" name="priority" value="Moderate"> Moderate
                    </label>
                    <label class="flex items-center gap-1 text-green-600">
                        <input type="radio" name="priority" value="Low"> Low
                    </label>
                </div>
            </div>

            <!-- Description + Upload -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium">Task Description</label>
                    <textarea id="activityDesc" name="description"
                        class="w-full h-28 border rounded-md px-2 py-1"
                        placeholder="Start writing here..." required></textarea>
                </div>

                <!-- Upload Image -->
                <div>
                    <label class="block text-sm font-medium">Upload Image</label>
                    <div class="border-2 border-dashed rounded-md h-28 flex flex-col items-center justify-center text-gray-400 cursor-pointer"
                        onclick="document.getElementById('activityImage').click()">
                        <span>Drag & Drop files here</span>
                        <span class="text-sm">or</span>
                        <button type="button" class="px-2 py-1 border rounded-md text-sm">Browse</button>
                        <input type="file" id="activityImage" name="image" class="hidden" accept="image/*">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-md">Done</button>
            </div>
        </form>
    </div>
</div>

<script> const kecelakaanId = {{ $data->id }};</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentEditingCard = null;
const csrfToken = document.querySelector('input[name="_token"]').value;

function openActivityModal(editBtn = null) {
    const form = document.getElementById("activityForm");
    form.reset();
    form.querySelectorAll('input[name="_method"]').forEach(e => e.remove());

    if(editBtn) {
        currentEditingCard = editBtn.closest(".border");
        const card = currentEditingCard;

        // Set form action + PUT
        form.action = `/kecelakaan/${kecelakaanId}/activities/${card.dataset.id}`;
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        // Isi form
        document.getElementById("activityId").value = card.dataset.id || '';
        document.getElementById("activityTitle").value = card.querySelector("h3")?.innerText || '';
        document.getElementById("activityDesc").value = card.querySelector("p")?.innerText || '';
        document.getElementById("activityDate").value = card.dataset.date || '';

        // Set priority radio
        const priorityValue = card.dataset.priority || '';
        const radio = form.querySelector(`input[name="priority"][value="${priorityValue}"]`);
        if(radio) radio.checked = true;

    } else {
        currentEditingCard = null;
        form.action = `/kecelakaan/${kecelakaanId}/activities`;
        form.method = 'POST';
    }

    document.getElementById("activityModal").classList.remove("hidden");
}

function closeActivityModal() {
    document.getElementById("activityModal").classList.add("hidden");
}

function saveActivity(e) {
    e.preventDefault();
    const form = document.getElementById('activityForm');
    const formData = new FormData(form);
    const methodOverride = form.querySelector('input[name="_method"]')?.value || 'POST';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-HTTP-Method-Override': methodOverride
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(!data.activity) throw new Error('Response invalid');
        Swal.fire('Berhasil!', 'Activity tersimpan.', 'success');
        closeActivityModal();

        if(currentEditingCard) {
            // Update card lama
            currentEditingCard.querySelector("h3").innerText = data.activity.title;
            currentEditingCard.querySelector("p").innerText = data.activity.description;
            currentEditingCard.dataset.priority = data.activity.priority || '';
            currentEditingCard.dataset.date = data.activity.date || '';
        } else {
            // Tambah card baru
            const container = document.getElementById("activity-list");

            const newCard = document.createElement("div");
            newCard.classList.add("border","rounded-md","p-4");
            newCard.dataset.id = data.activity.id;
            newCard.dataset.priority = data.activity.priority || '';
            newCard.dataset.date = data.activity.date || '';

            // Buat isi card
            const innerDiv = document.createElement("div");
            innerDiv.className = "flex justify-between items-start";

            const leftDiv = document.createElement("div");
            const h3 = document.createElement("h3");
            h3.className = "font-semibold text-red-700";
            h3.innerText = data.activity.title;
            const p = document.createElement("p");
            p.className = "text-sm text-gray-600 mt-1";
            p.innerText = data.activity.description;
            leftDiv.appendChild(h3);
            leftDiv.appendChild(p);

            const rightDiv = document.createElement("div");
            rightDiv.className = "flex gap-2";

            // Edit button
            const editBtn = document.createElement("button");
            editBtn.className = "bg-orange-200 text-red-700 px-2 py-1 rounded-md text-xs";
            editBtn.innerText = "Edit";
            editBtn.onclick = () => openActivityModal(editBtn);

            // Delete form
            const formDelete = document.createElement("form");
            formDelete.method = "POST";
            formDelete.action = `/kecelakaan/${kecelakaanId}/activities/${data.activity.id}`;

            const csrfInput = document.createElement("input");
            csrfInput.type = "hidden";
            csrfInput.name = "_token";
            csrfInput.value = csrfToken;

            const methodInput = document.createElement("input");
            methodInput.type = "hidden";
            methodInput.name = "_method";
            methodInput.value = "DELETE";

            const delBtn = document.createElement("button");
            delBtn.type = "submit";
            delBtn.className = "bg-red-200 text-red-700 px-2 py-1 rounded-md text-xs";
            delBtn.innerText = "Delete";
            delBtn.onclick = (event) => {
                event.preventDefault();
                deleteActivity(delBtn, formDelete);
            };

            formDelete.appendChild(csrfInput);
            formDelete.appendChild(methodInput);
            formDelete.appendChild(delBtn);

            rightDiv.appendChild(editBtn);
            rightDiv.appendChild(formDelete);

            innerDiv.appendChild(leftDiv);
            innerDiv.appendChild(rightDiv);

            newCard.appendChild(innerDiv);
            container.prepend(newCard);
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Gagal!', 'Activity tidak tersimpan.', 'error');
    });
}

function deleteActivity(button, form) {
    Swal.fire({
        title: 'Apakah yakin ingin menghapus activity ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-HTTP-Method-Override': 'DELETE'
                }
            }).then(() => {
                button.closest(".border").remove();
                Swal.fire({
                    icon: 'success',
                    title: 'Terhapus!',
                    text: 'Activity berhasil dihapus',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
    });
}
</script>
@endsection
