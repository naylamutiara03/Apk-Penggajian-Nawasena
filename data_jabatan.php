<?php
include("koneksi.php");
include("sidebar.php"); // Assuming sidebar.php sets $username
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <title>Data Jabatan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="bg-gray-100">
    <div class="p-6 lg:ml-[300px] flex-grow">
        <div id="successModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96 text-center">
                <h2 class="text-lg font-bold text-gray-800">Sukses!</h2>
                <p class="text-gray-600 mt-2" id="successMessage">Data berhasil diproses.</p>
                <div class="mt-4">
                    <button onclick="closeSuccessModal()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Tutup</button>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow mb-6">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <h1 class="text-2xl font-bold text-center lg:text-left">PT. Nawasena Sinergi Gemilang</h1>
                <div class="flex items-center mt-4 lg:mt-0">
                    <span class="mr-4">Selamat Datang,
                        <strong><?php echo htmlspecialchars($username); ?></strong></span>
                    <ion-icon name="person-circle-outline" class="text-4xl text-gray-500"></ion-icon>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold ml-2">Data Jabatan</h1>
            <span class="text-gray-500 mr-2"><?php echo date('d F Y'); ?></span>
        </div>
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center">
                <p class="mr-2">Pilih Berdasarkan:</p>
                <select id="filterJabatan" class="border rounded-lg p-2" onchange="filterJabatan()">
                    <option value="all">Semua Jabatan</option>
                    <option value="karyawan">Karyawan</option>
                    <option value="tukang">Tukang</option>
                </select>
            </div>
            <p class="text-sm italic text-gray-600">Notes: Gaji tukang yang tertera dibawah adalah hitungan per hari</p>
        </div>
        <div id="content">
            <div id="tableJabatan">
                <div class="flex justify-center">
                    <div class="bg-white p-6 mt-4 shadow-lg rounded-lg w-full max-w-7xl">
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white border border-gray-300 rounded-lg shadow-md">
                                <thead class="bg-blue-600 text-white text-sm uppercase font-semibold">
                                    <tr>
                                        <th class="py-4 px-6 text-center">No</th>
                                        <th class="py-4 px-6 text-left">Nama Jabatan</th>
                                        <th class="py-4 px-6 text-left">Gaji</th>
                                        <th class="py-4 px-6 text-left">Tunjangan Jabatan</th>
                                        <th class="py-4 px-6 text-left">Total</th>
                                        <th class="py-4 px-6 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700 text-sm" id="jabatanData">
                                    <?php
                                    $no = 1;
                                    $sql = mysqli_query($konek, "SELECT * FROM jabatan ORDER BY jabatan ASC");
                                    while ($d = mysqli_fetch_array($sql)) {
                                        $gaji_display = number_format($d['gapok'], 0, ',', '.');
                                        $tunjangan_display = '-';
                                        $total = 0;

                                        if ($d['jenis'] == 'karyawan') {
                                            $total = $d['gapok'] + $d['tunjangan_jabatan'];
                                            $tunjangan_display = number_format($d['tunjangan_jabatan'], 0, ',', '.');
                                        } else { // tukang
                                            $total = $d['gapok']; // gaji per hari
                                            // Explicitly show "Gaji per Hari" in the Gaji Pokok column for 'tukang'
                                            $gaji_display .= ' (per Hari)';
                                        }
                                        echo "<tr class='border-b border-gray-200 hover:bg-blue-100' data-jenis='{$d['jenis']}' data-id='{$d['id']}'>
                                                <td class='py-4 px-6 text-center font-bold'>$no</td>
                                                <td class='py-4 px-6'>{$d['jabatan']}</td>
                                                <td class='py-4 px-6'>" . $gaji_display . "</td>
                                                <td class='py-4 px-6'>" . $tunjangan_display . "</td>
                                                <td class='py-4 px-6'>" . number_format($total, 0, ',', '.') . "</td>
                                                <td class='py-4 px-6 text-center flex flex-col lg:flex-row gap-2 justify-center'>
                                                    <a href='#' onclick=\"openEditModal({$d['id']}, '{$d['jabatan']}', {$d['gapok']}, {$d['tunjangan_jabatan']}, '{$d['jenis']}')\" class='bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 flex items-center justify-center'>
                                                        <ion-icon name='pencil-outline' class='mr-1'></ion-icon> Edit
                                                    </a>
                                                    <a href='#' onclick=\"openDeleteModal('aksi_jabatan.php?act=delete&id={$d['id']}', {$d['id']})\" class='bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 flex items-center justify-center'>
                                                        <ion-icon name='trash-outline' class='mr-1'></ion-icon> Hapus
                                                    </a>
                                                </td>
                                            </tr>";
                                        $no++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6 flex justify-center">
                            <button onclick="openAddJabatanModal()"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                                <ion-icon name="person-add-outline" class="mr-1"></ion-icon> Tambah Jabatan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function filterJabatan() {
                const filterValue = document.getElementById('filterJabatan').value;
                const rows = document.querySelectorAll('#jabatanData tr');

                rows.forEach(row => {
                    if (filterValue === 'all' || row.getAttribute('data-jenis') === filterValue) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        </script>

        <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
            <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
                <h2 class="text-lg font-semibold mb-4">Konfirmasi Hapus</h2>
                <p>Apakah Anda yakin ingin menghapus jabatan ini?</p>
                <div class="flex justify-end mt-4">
                    <button id="cancelDelete"
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                    <button id="confirmDelete"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hapus</button>
                </div>
            </div>
        </div>

        <script>
            let deleteUrl = '';
            let deleteId = null; // Store the ID of the item to be deleted

            function openDeleteModal(url, id) {
                deleteUrl = url;
                deleteId = id;
                document.getElementById('deleteModal').classList.remove('hidden');
            }

            document.getElementById('confirmDelete').onclick = function () {
                fetch(deleteUrl, { method: 'GET' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('deleteModal').classList.add('hidden');
                            document.getElementById("successMessage").innerText = data.message;
                            document.getElementById("successModal").classList.remove("hidden");
                            
                            // Remove the row from the table
                            const rowToRemove = document.querySelector(`#jabatanData tr[data-id='${deleteId}']`);
                            if (rowToRemove) {
                                rowToRemove.remove();
                                // Re-number the remaining rows
                                updateRowNumbers();
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghapus data.');
                    });
            };

            document.getElementById('cancelDelete').onclick = function () {
                document.getElementById('deleteModal').classList.add('hidden');
            };

            function closeSuccessModal() {
                document.getElementById('successModal').classList.add('hidden');
            }

            // Function to update row numbers after an item is deleted
            function updateRowNumbers() {
                const remainingRows = document.querySelectorAll('#jabatanData tr');
                remainingRows.forEach((row, index) => {
                    row.children[0].innerText = index + 1; // Update the 'No' column
                });
            }
        </script>

        <?php include 'footer.php'; ?>
    </div>

</body>

</html>

<div id="addJabatanModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Tambah Data Jabatan</h2>
        <form id="addJabatanForm" method="POST">
            <div class="mb-4">
                <label for="addJenis" class="block text-sm font-medium text-gray-700">Jenis Jabatan</label>
                <select id="addJenis" name="jenis" onchange="toggleAddFormFields()" required
                    class="border rounded-lg p-2 w-full">
                    <option value="karyawan">Karyawan</option>
                    <option value="tukang">Tukang</option>
                </select>
            </div>

            <div class="mb-4" id="addNamaJabatanContainer">
                <label for="addNamaJabatan" class="block text-sm font-medium text-gray-700">Nama Jabatan</label>
                <input type="text" id="addNamaJabatan" name="jabatan" class="border rounded-lg p-2 w-full" required>
            </div>

            <div class="mb-4" id="addGapokContainer">
                <label for="addGapok" class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                <input type="text" id="addGapok" name="gapok" class="currency border rounded-lg p-2 w-full" required>
            </div>

            <div class="mb-4" id="addTunjanganContainer">
                <label for="addTunjangan" class="block text-sm font-medium text-gray-700">Tunjangan Jabatan</label>
                <input type="text" id="addTunjangan" name="tunjangan_jabatan" class="currency border rounded-lg p-2 w-full">
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeAddJabatanModal()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Tambah</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddJabatanModal() {
        const filter = document.getElementById("filterJabatan").value;
        if (filter === 'karyawan' || filter === 'tukang') {
            document.getElementById("addJenis").value = filter;
        } else {
            document.getElementById("addJenis").value = "karyawan";
        }
        // Reset form fields
        document.getElementById("addJabatanForm").reset();
        // Trigger the change event to set initial visibility of fields
        toggleAddFormFields();
        document.getElementById("addJabatanModal").classList.remove("hidden");
    }

    function closeAddJabatanModal() {
        document.getElementById("addJabatanModal").classList.add("hidden");
    }

    function toggleAddFormFields() {
        const jenis = document.getElementById("addJenis").value;
        const tunjanganContainer = document.getElementById("addTunjanganContainer");
        const tunjanganInput = document.getElementById("addTunjangan");
        const gapokLabel = document.getElementById("addGapok").previousElementSibling;

        if (jenis === "tukang") {
            tunjanganContainer.classList.add("hidden");
            tunjanganInput.removeAttribute("required");
            tunjanganInput.value = '0'; // Set tunjangan to 0 for tukang
            gapokLabel.innerText = "Gaji per Hari";
        } else { // karyawan
            tunjanganContainer.classList.remove("hidden");
            tunjanganInput.setAttribute("required", "required");
            gapokLabel.innerText = "Gaji Pokok";
        }
    }

    // Handle Add Form Submission with AJAX
    document.getElementById('addJabatanForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        const formData = new FormData(this);

        // Manually format currency fields by removing dots
        const gapokInput = document.getElementById('addGapok');
        const tunjanganInput = document.getElementById('addTunjangan');
        formData.set('gapok', gapokInput.value.replace(/\./g, ''));
        formData.set('tunjangan_jabatan', tunjanganInput.value.replace(/\./g, ''));

        fetch('aksi_jabatan.php?act=tambah', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeAddJabatanModal();
                document.getElementById("successMessage").innerText = data.message;
                document.getElementById("successModal").classList.remove("hidden");
                // Reload the page or dynamically add the new row to the table
                location.reload(); // Simple reload for now, dynamic add is more complex
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menambahkan data.');
        });
    });
</script>

<div id="editJabatanModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Edit Data Jabatan</h2>
        <form id="editJabatanForm" method="POST">
            <input type="hidden" name="id" id="editId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama Jabatan</label>
                <input type="text" id="editJabatan" name="jabatan" required class="border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label id="editGapokLabel" class="block text-sm font-medium text-gray-700">Gaji Pokok</label>
                <input type="text" id="editGapok" name="gapok" required class="currency border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4" id="editTunjanganContainer">
                <label class="block text-sm font-medium text-gray-700">Tunjangan Jabatan</label>
                <input type="text" id="editTunjangan" name="tunjangan_jabatan"
                    class="currency border rounded-lg p-2 w-full">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Jenis Jabatan</label>
                <select id="editJenis" name="jenis" required class="border rounded-lg p-2 w-full">
                    <option value="karyawan">Karyawan</option>
                    <option value="tukang">Tukang</option>
                </select>
            </div>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeEditJabatanModal()"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg mr-2 hover:bg-gray-600 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById("editJenis").addEventListener("change", function () {
        const jenis = this.value;
        const gapokLabel = document.getElementById("editGapokLabel");
        const tunjanganContainer = document.getElementById("editTunjanganContainer");
        const tunjanganInput = document.getElementById("editTunjangan");

        if (jenis === "tukang") {
            gapokLabel.innerText = "Gaji per Hari";
            tunjanganContainer.classList.add("hidden");
            tunjanganInput.removeAttribute("required");
            tunjanganInput.value = '0';
        } else {
            gapokLabel.innerText = "Gaji Pokok";
            tunjanganContainer.classList.remove("hidden");
            tunjanganInput.setAttribute("required", "required");
        }
    });

    function openEditModal(id, jabatan, gapok, tunjangan, jenis) {
        document.getElementById("editId").value = id;
        document.getElementById("editJabatan").value = jabatan;
        // Format the numeric values to currency strings for display in the modal
        document.getElementById("editGapok").value = new Intl.NumberFormat('id-ID').format(gapok);
        document.getElementById("editTunjangan").value = new Intl.NumberFormat('id-ID').format(tunjangan);
        document.getElementById("editJenis").value = jenis;

        // Manually trigger the change event to set initial visibility of fields in edit modal
        const event = new Event('change');
        document.getElementById("editJenis").dispatchEvent(event);

        document.getElementById("editJabatanModal").classList.remove("hidden");
    }

    function closeEditJabatanModal() {
        document.getElementById("editJabatanModal").classList.add("hidden");
    }

    // script untuk format mata uang
    document.querySelectorAll('.currency').forEach(input => {
        input.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, ''); // only digits
            value = new Intl.NumberFormat('id-ID').format(value); // format thousands
            this.value = value;
        });

        input.addEventListener('blur', function () {
            // Re-format on blur to ensure consistency
            let value = this.value.replace(/\D/g, '');
            this.value = new Intl.NumberFormat('id-ID').format(value);
        });
    });

    // Handle Edit Form Submission with AJAX
    document.getElementById('editJabatanForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get form data
        const formData = new FormData(this);

        // Manually format currency fields by removing dots
        const editGapokInput = document.getElementById('editGapok');
        const editTunjanganInput = document.getElementById('editTunjangan');
        formData.set('gapok', editGapokInput.value.replace(/\./g, ''));
        formData.set('tunjangan_jabatan', editTunjanganInput.value.replace(/\./g, ''));

        fetch('aksi_jabatan.php?act=edit', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditJabatanModal();
                document.getElementById("successMessage").innerText = data.message;
                document.getElementById("successModal").classList.remove("hidden");
                // Reload the page or dynamically update the row in the table
                location.reload(); // Simple reload for now, dynamic update is more complex
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate data.');
        });
    });
</script>