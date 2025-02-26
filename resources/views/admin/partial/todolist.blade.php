<style>
    
    /* Existing styles */
    .wrapper {
        background: transparent;
        border: 2px solid rgba(225, 225, 225, .2);
        backdrop-filter: blur(10px);
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        color: #FFFFFF;
        padding: 30px 40px;
    }

    .edit-form-control {
        width: 100%;
        background-color: transparent;
        color: #FFFFFF;
        border: none;
        outline: none;
        border: 2px solid rgba(255, 255, 255, .2);
        font-size: 16px;
        box-shadow: #000000 0 0 10px;
    }

    .edit-form-control:hover,
    .edit-form-control:focus {
        background-color: #FFFFFF;
        color: #000000;
    }

    .edit-form-control::placeholder {
        color: #FFFFFF;
    }

    .edit-form-control:hover::placeholder,
    .edit-form-control:focus::placeholder {
        color: #000000;
    }

    .sss-container {
        position: relative;
    }

    .sss-toggle-btn {
        position: fixed;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background-color: #5c2c00;
        color: white;
        border: none;
        padding: 10px;
        cursor: pointer;
        z-index: 1001;
        border-radius: 5px;
    }

    .sss-sidebar {
        position: fixed;
        right: -100%;
        top: 0;
        width: 88%;
        height: 100%;
        background-color: #191c24;
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.3);
        transition: right 0.5s ease;
        z-index: 1000;
        padding: 20px;
    }

    .sss-sidebar.open {
        right: 0;
    }

    .sss-clock-container {
        position: absolute;
        top: 50px;
        right: 0;
        transform: scale(0.72);
        margin: 0;
        padding: 0;
        border: 0;
        text-align: center;
    }

    .sss-clock {
        position: relative;
        width: 270px;
        height: 270px;
        border: 10px solid #5c2c00;
        border-radius: 50%;
        background: #ffffff;
    }

    .sss-number {
        position: absolute;
        top: 46%;
        left: 48%;
        font-size: 18px;
        font-weight: bold;
        color: black;
        /* Reduced translate distance from 115px to 105px */
        transform: rotate(var(--rotate, 0deg)) translate(0, -115px) rotate(calc(-1 * var(--rotate, 0deg)));
    }

    /* Set custom angles for each number */
    .sss-number12 {
        --rotate: 0deg;
    }

    .sss-number1 {
        --rotate: 30deg;
    }

    .sss-number2 {
        --rotate: 60deg;
    }

    .sss-number3 {
        --rotate: 90deg;
    }

    .sss-number4 {
        --rotate: 120deg;
    }

    .sss-number5 {
        --rotate: 150deg;
    }

    .sss-number6 {
        --rotate: 180deg;
    }

    .sss-number7 {
        --rotate: 210deg;
    }

    .sss-number8 {
        --rotate: 240deg;
    }

    .sss-number9 {
        --rotate: 270deg;
    }

    .sss-number10 {
        --rotate: 300deg;
    }

    .sss-number11 {
        --rotate: 330deg;
    }

    .sss-hand {
        position: absolute;
        bottom: 50%;
        left: 50%;
        background: black;
        transform-origin: bottom;
        transform: translateX(-50%) rotate(0deg);
        border-radius: 5px;
    }

    .sss-hour-hand {
        width: 6px;
        height: 54px;
        background: black;
    }

    .sss-minute-hand {
        width: 4px;
        height: 90px;
        background: #ffa500;
    }

    .sss-second-hand {
        width: 2px;
        height: 108px;
        background: red;
    }

    .sss-center {
        position: absolute;
        width: 10px;
        height: 10px;
        background: white;
        border: 2px solid black;
        border-radius: 50%;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .sss-HqitButton-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
    }

    .sss-add-note-btn,
    .sss-btn-select-file {
        background: #ffa500;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .sss-note {
        position: absolute;
        width: 250px;
        background: rgb(29, 28, 28);
        border: 3px solid #ccc;
        padding: 13px;
        border-radius: 9px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        cursor: grab;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .sss-note-header {
        font-weight: bold;
        text-align: center;
        padding: 5px;
        border-bottom: 1px solid #ccc;
        background-color: #f9f9f9;
        cursor: text;
        color: black;
        border-radius: 7px;
    }
</style>

<div class="sss-container">
    <button class="sss-toggle-btn">☰</button>
    <div class="sss-sidebar" id="sss-sidebar">
        <div class="sss-clock-container">
            <div class="sss-clock">
                <div class="sss-number sss-number12">12</div>
                <div class="sss-number sss-number1">1</div>
                <div class="sss-number sss-number2">2</div>
                <div class="sss-number sss-number3">3</div>
                <div class="sss-number sss-number4">4</div>
                <div class="sss-number sss-number5">5</div>
                <div class="sss-number sss-number6">6</div>
                <div class="sss-number sss-number7">7</div>
                <div class="sss-number sss-number8">8</div>
                <div class="sss-number sss-number9">9</div>
                <div class="sss-number sss-number10">10</div>
                <div class="sss-number sss-number11">11</div>
                <div class="sss-hand sss-hour-hand"></div>
                <div class="sss-hand sss-minute-hand"></div>
                <div class="sss-hand sss-second-hand"></div>
                <div class="sss-center"></div>
            </div>
            <div class="sss-HqitButton-container">
                <button class="sss-add-note-btn" id="sss-addNoteBtn">Tạo Note</button>
                <button class="sss-btn-select-file" id="sss-chooseImageBtn">Chọn Ảnh</button>
            </div>
            <input type="file" class="form-control" id="sss-imageInput" accept="image/*" style="display: none;">
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.sss-toggle-btn');
        const sidebar = document.getElementById('sss-sidebar');
        const addNoteBtn = document.getElementById('sss-addNoteBtn');
        const chooseImageBtn = document.getElementById('sss-chooseImageBtn');
        const imageInput = document.getElementById('sss-imageInput');

        // Toggle sidebar open/close
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Clock functionality
        const hourHand = document.querySelector('.sss-hour-hand');
        const minuteHand = document.querySelector('.sss-minute-hand');
        const secondHand = document.querySelector('.sss-second-hand');

        function setClock() {
            const now = new Date();
            const seconds = now.getSeconds();
            const minutes = now.getMinutes();
            const hours = now.getHours();
            secondHand.style.transform = `translateX(-50%) rotate(${seconds * 6}deg)`;
            minuteHand.style.transform = `translateX(-50%) rotate(${minutes * 6 + seconds / 10}deg)`;
            hourHand.style.transform = `translateX(-50%) rotate(${hours * 30 + minutes / 2}deg)`;
        }
        setInterval(setClock, 1000);
        setClock();



        // Load saved sidebar background image from localStorage
        const savedSidebarBg = localStorage.getItem('sssSidebarBG');
        if (savedSidebarBg) {
            sidebar.style.backgroundImage = `url(${savedSidebarBg})`;
            sidebar.style.backgroundSize = 'cover';
            sidebar.style.backgroundPosition = 'center';
        }

        // Change sidebar background image via file input & save to localStorage
        chooseImageBtn.addEventListener('click', () => {
            imageInput.click();
        });

        imageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    sidebar.style.backgroundImage = `url(${event.target.result})`;
                    sidebar.style.backgroundSize = 'cover';
                    sidebar.style.backgroundPosition = 'center';
                    localStorage.setItem('sssSidebarBG', event.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Save all notes into localStorage using innerHTML for content
        function saveNotes() {
            const noteElements = document.querySelectorAll('.sss-note');
            const notesData = [];
            noteElements.forEach(note => {
                const id = note.getAttribute('data-id');
                const header = note.querySelector('.sss-note-header').textContent;
                const content = note.querySelector('div[contenteditable]:not(.sss-note-header)').innerHTML;
                const top = note.style.top;
                const left = note.style.left;
                notesData.push({
                    id,
                    header,
                    content,
                    top,
                    left
                });
            });
            localStorage.setItem('sssNotesData', JSON.stringify(notesData));
        }

        // Create a new note element with placeholder behavior and delete button
        function createNote(noteData = null) {
            const note = document.createElement('div');
            note.className = 'sss-note';
            const id = noteData ? noteData.id : Date.now();
            note.setAttribute('data-id', id);
            note.style.top = noteData ? noteData.top : '100px';
            note.style.left = noteData ? noteData.left : '50px';

            // Delete button
            const deleteBtn = document.createElement('span');
            deleteBtn.textContent = '✖';
            deleteBtn.style.position = 'absolute';
            deleteBtn.style.top = '5px';
            deleteBtn.style.right = '5px';
            deleteBtn.style.color = 'black';
            deleteBtn.style.cursor = 'pointer';
            deleteBtn.style.width = '20px';
            deleteBtn.style.height = '20px';
            deleteBtn.style.border = '2px solid white';
            deleteBtn.style.borderRadius = '50%';
            deleteBtn.style.display = 'flex';
            deleteBtn.style.alignItems = 'center';
            deleteBtn.style.justifyContent = 'center';
            deleteBtn.style.background = 'white';
            // Remove note on click and update localStorage
            deleteBtn.addEventListener('click', () => {
                note.remove();
                saveNotes();
            });
            note.appendChild(deleteBtn);

            const noteHeader = document.createElement('div');
            noteHeader.className = 'sss-note-header';
            noteHeader.contentEditable = true;
            noteHeader.textContent = noteData ? noteData.header : 'Tiêu đề';

            const noteContent = document.createElement('div');
            noteContent.contentEditable = true;
            // Use innerHTML so that saved line breaks can be restored.
            noteContent.innerHTML = noteData ? noteData.content : 'Nội dung ghi chú...';

            // Placeholder simulation for header
            noteHeader.addEventListener('focus', () => {
                if (noteHeader.textContent === 'Tiêu đề') {
                    noteHeader.textContent = '';
                }
            });
            noteHeader.addEventListener('blur', () => {
                if (noteHeader.textContent.trim() === '') {
                    noteHeader.textContent = 'Tiêu đề';
                }
                saveNotes();
            });

            // Placeholder simulation for content with innerHTML
            noteContent.addEventListener('focus', () => {
                if (noteContent.innerHTML === 'Nội dung ghi chú...') {
                    noteContent.innerHTML = '';
                }
            });
            noteContent.addEventListener('blur', () => {
                if (noteContent.innerHTML.trim() === '') {
                    noteContent.innerHTML = 'Nội dung ghi chú...';
                }
                saveNotes();
            });

            note.appendChild(noteHeader);
            note.appendChild(noteContent);
            sidebar.appendChild(note);

            // Drag behavior for the note
            let offsetX = 0,
                offsetY = 0,
                isDragging = false;
            note.addEventListener('mousedown', (e) => {
                isDragging = true;
                offsetX = e.clientX - note.offsetLeft;
                offsetY = e.clientY - note.offsetTop;
                note.style.cursor = 'grabbing';
            });
            document.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                const sidebarRect = sidebar.getBoundingClientRect();
                const noteWidth = note.offsetWidth;
                const noteHeight = note.offsetHeight;
                let newLeft = e.clientX - offsetX;
                let newTop = e.clientY - offsetY;
                if (newLeft < 0) newLeft = 0;
                if (newTop < 0) newTop = 0;
                if (newLeft + noteWidth > sidebarRect.width) newLeft = sidebarRect.width - noteWidth;
                if (newTop + noteHeight > sidebarRect.height) newTop = sidebarRect.height - noteHeight;
                note.style.left = `${newLeft}px`;
                note.style.top = `${newTop}px`;
            });
            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    note.style.cursor = 'grab';
                    saveNotes();
                }
            });
        }

        // Load saved notes from localStorage
        const savedNotes = localStorage.getItem('sssNotesData');
        if (savedNotes) {
            const notesArr = JSON.parse(savedNotes);
            notesArr.forEach(data => {
                createNote(data);
            });
        }

        // Create new note on button click
        addNoteBtn.addEventListener('click', () => {
            createNote();
            saveNotes();
        });


    });
</script>