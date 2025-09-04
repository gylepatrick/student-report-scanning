<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Student Misbehaviour Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body{background:#0f172a;color:#e2e8f0}
    .card{border-radius:1rem;box-shadow:0 10px 30px rgba(0,0,0,.25)}
    #preview{width:100%;max-height:50vh;object-fit:cover;border-radius:.75rem}
    .scan-area{position:relative}
    .scan-overlay{position:absolute;inset:0;border:2px dashed rgba(255,255,255,.35);border-radius:.75rem;pointer-events:none}
    .scan-line{position:absolute;left:0;right:0;height:2px;background:rgba(255,255,255,.8);animation:scan 2.2s linear infinite}
    @keyframes scan{0%{top:10%}100%{top:90%}}
  </style>
</head>
<body>
  <div class="container py-4 py-md-5">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="card bg-dark border-0">
          <div class="card-body p-4 p-md-5">
            <h1 class="h4 mb-4">📋 Student Misbehaviour Report</h1>
            <div class="row g-4">
              
              <!-- Left side: Scanner (hidden first) -->
              <div class="col-12 col-md-6">
                <div class="mb-3">
                  <button id="btnToggleScanner" class="btn btn-primary">📷 Scan ID</button>
                </div>
                <div id="scannerSection" class="d-none">
                  <div class="scan-area">
                    <video id="preview" class="bg-black" autoplay muted playsinline></video>
                    <div class="scan-overlay rounded">
                      <div class="scan-line"></div>
                    </div>
                  </div>
                  <div class="d-flex gap-2 mt-3 flex-wrap">
                    <button id="btnStart" class="btn btn-success">▶ Start</button>
                    <button id="btnStop" class="btn btn-outline-light" disabled>⏹ Stop</button>
                  </div>
                </div>
              </div>

              <!-- Right side: Report form -->
              <div class="col-12 col-md-6">
                <form id="reportForm" method="post" action="save_report.php" class="text-dark">
                  <div class="mb-3">
                    <label class="form-label">Student Name</label>
                    <input type="text" id="studentName" name="studentName" class="form-control" readonly required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Misbehaviour</label>
                    <select name="misbehaviour" class="form-select" required>
                      <option value="">-- Select --</option>
                      <option value="Late to class">Late to class</option>
                      <option value="Disruptive behavior">Disruptive behavior</option>
                      <option value="Not in uniform">Not in uniform</option>
                      <option value="Bullying">Bullying</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <input type="hidden" id="studentCode" name="studentCode">
                  <button type="submit" class="btn btn-success w-100">💾 Save Report</button>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module">
    import { BrowserMultiFormatReader, BarcodeFormat, DecodeHintType, NotFoundException } from 'https://cdn.jsdelivr.net/npm/@zxing/library@0.20.0/+esm';

    const video = document.getElementById('preview');
    const btnStart = document.getElementById('btnStart');
    const btnStop = document.getElementById('btnStop');
    const btnToggleScanner = document.getElementById('btnToggleScanner');
    const scannerSection = document.getElementById('scannerSection');
    const studentName = document.getElementById('studentName');
    const studentCode = document.getElementById('studentCode');

    let codeReader;
    let currentStream;

    // Toggle scanner visibility
    btnToggleScanner.addEventListener("click", () => {
      scannerSection.classList.toggle("d-none");
    });

    async function start(){
      stop();
      codeReader = new BrowserMultiFormatReader(new Map([
        [DecodeHintType.POSSIBLE_FORMATS, [BarcodeFormat.QR_CODE, BarcodeFormat.CODE_128]]
      ]));
      btnStart.disabled = true;
      btnStop.disabled = false;
      try {
        await codeReader.decodeFromVideoDevice(undefined, video, (result, err) => {
          if(result){ handleScan(result.getText()); }
          if(err && !(err instanceof NotFoundException)) console.error(err);
          currentStream = video.srcObject;
        });
      } catch(e){ console.error(e); }
    }

    function stop(){
      if(codeReader){ try{ codeReader.reset(); }catch{} codeReader = null; }
      if(currentStream){ currentStream.getTracks().forEach(t=>t.stop()); currentStream=null; }
      btnStart.disabled=false; btnStop.disabled=true;
    }

    function handleScan(code){
      studentCode.value = code;
      // Fetch student name from backend
      fetch("get_student.php?code="+encodeURIComponent(code))
        .then(r=>r.json())
        .then(data=>{
          if(data.status==="success"){
            studentName.value = data.name;
          } else {
            studentName.value = "Unknown Student";
          }
        })
        .catch(()=>studentName.value = "Error fetching name");
    }

    btnStart.addEventListener('click', start);
    btnStop.addEventListener('click', stop);

    // Just request permission once
    (async function init(){
      try{ const tmp = await navigator.mediaDevices.getUserMedia({ video: true }); tmp.getTracks().forEach(t=>t.stop()); }catch{}
    })();
  </script>
</body>
</html>
