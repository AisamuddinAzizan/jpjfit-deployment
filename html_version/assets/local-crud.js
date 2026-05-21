(function () {
  "use strict";

  var PATH_RE = /\/html_version\/([^\/]+)\/(index|create|edit|show)\.html$/i;
  var RESOURCE_CONFIG = {
    "users": {
      key: "jpjfit_users",
      columns: [
        { label: "Name", value: function (r) { return r.name; } },
        { label: "Email", value: function (r) { return r.email; } },
        { label: "Role", value: function (r) { return prettyRole(r.role); } },
        { label: "Status", value: function (r) { return toBool(r.is_active) ? "Active" : "Inactive"; }, badge: true }
      ],
      showMap: {
        "name": function (r) { return r.name; },
        "email": function (r) { return r.email; },
        "phone": function (r) { return r.phone; },
        "department": function (r) { return r.department; },
        "role": function (r) { return prettyRole(r.role); },
        "status": function (r) { return toBool(r.is_active) ? "Active" : "Inactive"; }
      }
    },
    "participants": {
      key: "jpjfit_participants",
      columns: [
        { label: "No", value: function (r) { return r.participant_no; } },
        { label: "Name", value: function (r) { return r.full_name; } },
        { label: "IC No", value: function (r) { return r.ic_no; } },
        { label: "Agency", value: function (r) { return r.agency; } },
        { label: "Status", value: function (r) { return toBool(r.is_active) ? "Active" : "Inactive"; }, badge: true }
      ],
      showMap: {
        "participant no": function (r) { return r.participant_no; },
        "full name": function (r) { return r.full_name; },
        "ic no": function (r) { return r.ic_no; },
        "gender": function (r) { return r.gender; },
        "phone": function (r) { return r.phone; },
        "agency": function (r) { return r.agency; }
      }
    },
    "test-sessions": {
      key: "jpjfit_test_sessions",
      columns: [
        { label: "Code", value: function (r) { return r.session_code; } },
        { label: "Title", value: function (r) { return r.title; } },
        { label: "Date", value: function (r) { return r.session_date; } },
        { label: "Location", value: function (r) { return r.location; } },
        { label: "Participants", value: function (r) { return arrayCount(r["participant_ids[]"]); } },
        { label: "Status", value: function (r) { return r.status || "scheduled"; }, badge: true }
      ],
      showMap: {
        "code": function (r) { return r.session_code; },
        "title": function (r) { return r.title; },
        "date": function (r) { return r.session_date; },
        "location": function (r) { return r.location; },
        "status": function (r) { return r.status || "scheduled"; }
      }
    },
    "fitness-results": {
      key: "jpjfit_fitness_results",
      columns: [
        { label: "Participant", value: function (r) { return r.participant_id__label || r.participant_id; } },
        { label: "Session", value: function (r) { return r.test_session_id__label || r.test_session_id; } },
        { label: "Score", value: function (r) { return computeFitnessScore(r); } },
        { label: "Classification", value: function (r) { return classifyFitness(computeFitnessScore(r)); } },
        { label: "Result", value: function (r) { return computeFitnessScore(r) >= 70 ? "Pass" : "Fail"; }, badge: true }
      ],
      showMap: {
        "participant": function (r) { return r.participant_id__label || r.participant_id; },
        "session": function (r) { return r.test_session_id__label || r.test_session_id; },
        "recorded by": function () { return "Admin User"; },
        "push-ups": function (r) { return r.push_ups; },
        "sit-ups": function (r) { return r.sit_ups; },
        "sit & reach": function (r) { return addUnit(r.sit_and_reach_cm, "cm"); },
        "shuttle run": function (r) { return r.shuttle_run_level; },
        "2.4 km": function (r) { return addUnit(r.run_2_4km_seconds, "sec"); },
        "total score": function (r) { return String(computeFitnessScore(r)); },
        "classification": function (r) { return classifyFitness(computeFitnessScore(r)); },
        "result": function (r) { return computeFitnessScore(r) >= 70 ? "Pass" : "Fail"; }
      }
    },
    "health-records": {
      key: "jpjfit_health_records",
      columns: [
        { label: "Participant", value: function (r) { return r.participant_id__label || r.participant_id; } },
        { label: "Session", value: function (r) { return r.test_session_id__label || r.test_session_id; } },
        { label: "BMI", value: function (r) { return computeBmi(r.height_cm, r.weight_kg); } },
        { label: "Blood Pressure", value: function (r) { return joinNonEmpty([r.blood_pressure_systolic, r.blood_pressure_diastolic], "/"); } },
        { label: "Glucose", value: function (r) { return r.glucose_mmol; } }
      ],
      showMap: {
        "participant": function (r) { return r.participant_id__label || r.participant_id; },
        "session": function (r) { return r.test_session_id__label || r.test_session_id; },
        "recorded by": function () { return "Admin User"; },
        "height": function (r) { return addUnit(r.height_cm, "cm"); },
        "weight": function (r) { return addUnit(r.weight_kg, "kg"); },
        "bmi": function (r) { return computeBmi(r.height_cm, r.weight_kg); },
        "blood pressure": function (r) { return joinNonEmpty([r.blood_pressure_systolic, r.blood_pressure_diastolic], "/"); },
        "glucose": function (r) { return r.glucose_mmol; },
        "cholesterol": function (r) { return r.cholesterol_mmol; }
      }
    }
  };

  var route = getRoute();
  if (!route || !RESOURCE_CONFIG[route.resource]) {
    return;
  }

  var config = RESOURCE_CONFIG[route.resource];
  if (route.page === "create" || route.page === "edit") {
    initFormPage(config, route);
  }
  if (route.page === "index") {
    initIndexPage(config, route);
  }
  if (route.page === "show") {
    initShowPage(config, route);
  }

  function getRoute() {
    var p = window.location.pathname.replace(/\\/g, "/");
    var match = p.match(PATH_RE);
    if (!match) {
      return null;
    }
    return { resource: match[1], page: match[2] };
  }

  function initFormPage(cfg, routeInfo) {
    var form = findCrudForm();
    if (!form) {
      return;
    }

    var records = loadRecords(cfg.key);
    var qs = new URLSearchParams(window.location.search);
    var id = qs.get("id");

    if (routeInfo.page === "edit" && id) {
      var existing = findById(records, id);
      if (existing) {
        fillForm(form, existing);
      }
    }

    form.addEventListener("submit", function (event) {
      event.preventDefault();
      var record = formToRecord(form);
      var nowId = id || record.id || generateId();
      record.id = nowId;

      if (routeInfo.page === "edit") {
        records = updateRecord(records, record);
      } else {
        records.push(record);
      }

      saveRecords(cfg.key, records);
      window.location.href = "index.html?saved=1";
    });
  }

  function initIndexPage(cfg) {
    var table = document.querySelector("table.data-table") || document.querySelector("table");
    if (!table) {
      return;
    }
    var tbody = table.querySelector("tbody");
    if (!tbody) {
      return;
    }

    var records = loadRecords(cfg.key);
    if (!records.length) {
      return;
    }

    renderTableRows(tbody, table, records, cfg);

    tbody.addEventListener("click", function (event) {
      var target = event.target;
      if (!(target instanceof HTMLElement)) {
        return;
      }
      if (target.matches("[data-delete-id]")) {
        event.preventDefault();
        var id = target.getAttribute("data-delete-id");
        if (!id) {
          return;
        }

        if (!window.confirm("Delete this record?")) {
          return;
        }

        var next = records.filter(function (r) { return String(r.id) !== String(id); });
        saveRecords(cfg.key, next);
        records = next;
        renderTableRows(tbody, table, records, cfg);
      }
    });
  }

  function initShowPage(cfg) {
    var qs = new URLSearchParams(window.location.search);
    var id = qs.get("id");
    if (!id) {
      return;
    }

    var record = findById(loadRecords(cfg.key), id);
    if (!record) {
      return;
    }

    var mapping = cfg.showMap || {};
    var dts = document.querySelectorAll("dl dt");
    dts.forEach(function (dt) {
      var label = normalizeLabel(dt.textContent || "");
      var dd = dt.nextElementSibling;
      if (!dd || !mapping[label]) {
        return;
      }
      var val = mapping[label](record);
      if (val === undefined || val === null || val === "") {
        val = "-";
      }

      var badge = dd.querySelector("span");
      if (badge) {
        badge.textContent = String(val);
        return;
      }
      dd.textContent = String(val);
    });

    var editLink = document.querySelector('a[href="edit.html"], a[href$="edit.html"]');
    if (editLink) {
      editLink.setAttribute("href", "edit.html?id=" + encodeURIComponent(String(record.id)));
    }
  }

  function renderTableRows(tbody, table, records, cfg) {
    var thCount = table.querySelectorAll("thead th").length;
    tbody.innerHTML = "";

    if (!records.length) {
      var empty = document.createElement("tr");
      var td = document.createElement("td");
      td.colSpan = Math.max(thCount, cfg.columns.length + 1);
      td.className = "text-center text-slate-500";
      td.textContent = "No records found.";
      empty.appendChild(td);
      tbody.appendChild(empty);
      return;
    }

    records.forEach(function (record) {
      var tr = document.createElement("tr");

      cfg.columns.forEach(function (col) {
        var td = document.createElement("td");
        var value = col.value(record);
        if (col.badge) {
          var span = document.createElement("span");
          span.className = badgeClass(String(value));
          span.textContent = safeText(value);
          td.appendChild(span);
        } else {
          td.textContent = safeText(value);
        }
        tr.appendChild(td);
      });

      var actions = document.createElement("td");
      actions.className = "text-right";
      actions.innerHTML =
        '<a href="show.html?id=' + encodeURIComponent(String(record.id)) + '" class="text-sm font-semibold text-sky-700">View</a>' +
        '<a href="edit.html?id=' + encodeURIComponent(String(record.id)) + '" class="ml-3 text-sm font-semibold text-teal-700">Edit</a>' +
        '<button data-delete-id="' + encodeURIComponent(String(record.id)) + '" class="ml-3 text-sm font-semibold text-rose-700" type="button">Delete</button>';
      tr.appendChild(actions);

      tbody.appendChild(tr);
    });
  }

  function findCrudForm() {
    var forms = Array.prototype.slice.call(document.querySelectorAll("form"));
    return forms.find(function (f) {
      var method = (f.getAttribute("method") || "").toUpperCase();
      return method === "POST";
    }) || null;
  }

  function formToRecord(form) {
    var fd = new FormData(form);
    var record = {};

    fd.forEach(function (value, key) {
      if (record[key] === undefined) {
        record[key] = value;
        return;
      }

      // Keep true list fields as arrays, but for normal fields keep the last value.
      if (key.slice(-2) === "[]") {
        if (!Array.isArray(record[key])) {
          record[key] = [record[key]];
        }
        record[key].push(value);
      } else {
        record[key] = value;
      }
    });

    Array.prototype.forEach.call(form.elements, function (el) {
      if (!(el instanceof HTMLElement)) {
        return;
      }
      if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement)) {
        return;
      }
      if (!el.name) {
        return;
      }
      if (el instanceof HTMLInputElement && el.type === "checkbox" && !fd.has(el.name)) {
        record[el.name] = "0";
      }
      if (el instanceof HTMLSelectElement) {
        var selected = el.options[el.selectedIndex];
        if (selected) {
          record[el.name + "__label"] = selected.textContent.trim();
        }
      }
    });

    record.updated_at = new Date().toISOString();
    return record;
  }

  function fillForm(form, record) {
    Array.prototype.forEach.call(form.elements, function (el) {
      if (!(el instanceof HTMLElement)) {
        return;
      }
      if (!(el instanceof HTMLInputElement || el instanceof HTMLSelectElement || el instanceof HTMLTextAreaElement)) {
        return;
      }
      if (!el.name || record[el.name] === undefined) {
        return;
      }

      var val = record[el.name];
      if (el instanceof HTMLInputElement && el.type === "checkbox") {
        if (Array.isArray(val)) {
          el.checked = val.indexOf(el.value) !== -1;
        } else {
          el.checked = toBool(val) || String(val) === String(el.value);
        }
        return;
      }

      if (Array.isArray(val)) {
        if (el instanceof HTMLSelectElement && el.multiple) {
          Array.prototype.forEach.call(el.options, function (opt) {
            opt.selected = val.indexOf(opt.value) !== -1;
          });
        }
        return;
      }

      el.value = String(val);
    });
  }

  function loadRecords(key) {
    try {
      var raw = window.localStorage.getItem(key);
      if (!raw) {
        return [];
      }
      var parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function saveRecords(key, records) {
    window.localStorage.setItem(key, JSON.stringify(records));
  }

  function updateRecord(records, record) {
    var found = false;
    var next = records.map(function (item) {
      if (String(item.id) === String(record.id)) {
        found = true;
        return record;
      }
      return item;
    });
    if (!found) {
      next.push(record);
    }
    return next;
  }

  function findById(records, id) {
    return records.find(function (r) { return String(r.id) === String(id); }) || null;
  }

  function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
  }

  function computeFitnessScore(record) {
    var push = toNumber(record.push_ups);
    var sit = toNumber(record.sit_ups);
    var reach = toNumber(record.sit_and_reach_cm);
    var shuttle = toNumber(record.shuttle_run_level) * 3;
    var runPenalty = Math.max(0, 1000 - toNumber(record.run_2_4km_seconds)) / 40;
    var total = push + sit + reach + shuttle + runPenalty;
    return Math.max(0, Math.min(100, Math.round(total)));
  }

  function classifyFitness(score) {
    if (score >= 85) {
      return "Excellent";
    }
    if (score >= 70) {
      return "Good";
    }
    if (score >= 50) {
      return "Average";
    }
    return "Needs Improvement";
  }

  function computeBmi(heightCm, weightKg) {
    var h = toNumber(heightCm) / 100;
    var w = toNumber(weightKg);
    if (!h || !w) {
      return "-";
    }
    return (w / (h * h)).toFixed(1);
  }

  function toNumber(v) {
    var n = Number(v);
    return Number.isFinite(n) ? n : 0;
  }

  function toBool(v) {
    if (Array.isArray(v)) {
      return v.some(function (item) { return toBool(item); });
    }
    return String(v) === "1" || String(v).toLowerCase() === "true" || String(v).toLowerCase() === "yes" || String(v).toLowerCase() === "on";
  }

  function normalizeLabel(text) {
    return text.replace(/\s+/g, " ").trim().toLowerCase();
  }

  function safeText(value) {
    if (value === undefined || value === null || value === "") {
      return "-";
    }
    if (Array.isArray(value)) {
      return value.join(", ");
    }
    return String(value);
  }

  function joinNonEmpty(values, sep) {
    var list = values.filter(function (v) { return v !== undefined && v !== null && v !== ""; });
    return list.length ? list.join(sep) : "-";
  }

  function addUnit(value, unit) {
    var text = safeText(value);
    return text === "-" ? text : text + " " + unit;
  }

  function arrayCount(value) {
    if (Array.isArray(value)) {
      return String(value.length);
    }
    if (!value) {
      return "0";
    }
    return "1";
  }

  function badgeClass(value) {
    var v = String(value).toLowerCase();
    if (v === "active" || v === "pass" || v === "good" || v === "excellent") {
      return "rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700";
    }
    if (v === "inactive" || v === "fail") {
      return "rounded-full bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700";
    }
    return "rounded-full bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-700";
  }

  function prettyRole(role) {
    if (!role) {
      return "-";
    }
    return String(role).replace(/_/g, " ").replace(/\b\w/g, function (m) { return m.toUpperCase(); });
  }
})();
