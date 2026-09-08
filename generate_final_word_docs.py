import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
import os

doc = docx.Document()

# Page Margins
for section in doc.sections:
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)

def style_table_header(row, col_widths, bg_color="1F4E79"):
    for idx, cell in enumerate(row.cells):
        cell.width = Inches(col_widths[idx])
        shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{bg_color}"/>')
        cell._tc.get_or_add_tcPr().append(shading)
        for paragraph in cell.paragraphs:
            paragraph.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for run in paragraph.runs:
                run.font.bold = True
                run.font.color.rgb = RGBColor(255, 255, 255)
                run.font.size = Pt(9.5)
                run.font.name = 'Calibri'

def style_table_row(row, col_widths, is_even=False):
    bg_color = "F2F4F7" if is_even else "FFFFFF"
    for idx, cell in enumerate(row.cells):
        cell.width = Inches(col_widths[idx])
        if is_even:
            shading = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{bg_color}"/>')
            cell._tc.get_or_add_tcPr().append(shading)
        for paragraph in cell.paragraphs:
            for run in paragraph.runs:
                run.font.size = Pt(9)
                run.font.name = 'Calibri'

def add_callout(doc, title, text, bg_color="EBF3FA", border_color="2563EB"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    cell.width = Inches(6.5)
    
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{bg_color}"/>')
    tcPr.append(shd)
    
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>'
        f'<w:top w:val="none"/>'
        f'<w:left w:val="single" w:sz="36" w:space="0" w:color="{border_color}"/>'
        f'<w:bottom w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(2)
    r_title = p.add_run(f"📌 {title}\n")
    r_title.bold = True
    r_title.font.name = 'Calibri'
    r_title.font.size = Pt(10)
    r_title.font.color.rgb = RGBColor(31, 78, 121)
    
    r_text = p.add_run(text)
    r_text.font.name = 'Calibri'
    r_text.font.size = Pt(9.5)
    r_text.font.color.rgb = RGBColor(51, 51, 51)
    doc.add_paragraph().paragraph_format.space_after = Pt(4)

def add_heading_1(text):
    h = doc.add_heading(text, level=1)
    h.paragraph_format.space_before = Pt(14)
    h.paragraph_format.space_after = Pt(4)
    for r in h.runs:
        r.font.name = 'Calibri'
        r.font.color.rgb = RGBColor(31, 78, 121)

def add_heading_2(text):
    h = doc.add_heading(text, level=2)
    h.paragraph_format.space_before = Pt(10)
    h.paragraph_format.space_after = Pt(3)
    for r in h.runs:
        r.font.name = 'Calibri'
        r.font.color.rgb = RGBColor(59, 130, 246)

def add_heading_3(text):
    h = doc.add_heading(text, level=3)
    h.paragraph_format.space_before = Pt(6)
    h.paragraph_format.space_after = Pt(2)
    for r in h.runs:
        r.font.name = 'Calibri'
        r.font.color.rgb = RGBColor(31, 41, 55)

def add_bullet(text, bold_prefix=""):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_after = Pt(2)
    if bold_prefix:
        r_b = p.add_run(bold_prefix)
        r_b.bold = True
        r_b.font.name = 'Calibri'
        r_b.font.size = Pt(9.5)
    r_t = p.add_run(text)
    r_t.font.name = 'Calibri'
    r_t.font.size = Pt(9.5)

# =========================================================================
# TITLE & METADATA
# =========================================================================
title_p = doc.add_paragraph()
title_p.paragraph_format.space_before = Pt(24)
title_p.paragraph_format.space_after = Pt(4)
r_main_title = title_p.add_run("BANSAGAR RESERVOIR FISHERIES ERP")
r_main_title.font.name = 'Calibri'
r_main_title.font.size = Pt(24)
r_main_title.font.bold = True
r_main_title.font.color.rgb = RGBColor(31, 78, 121)

sub_p = doc.add_paragraph()
sub_p.paragraph_format.space_after = Pt(14)
r_sub = sub_p.add_run("Final Comprehensive Technical Architecture, Production File Structure, and System Specification")
r_sub.font.name = 'Calibri'
r_sub.font.size = Pt(13)
r_sub.font.italic = True
r_sub.font.color.rgb = RGBColor(89, 89, 89)

meta_tbl = doc.add_table(rows=6, cols=2)
meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
meta_data = [
    ("Target Platform:", "Private Web Admin ERP (Desktop / Laptop Chrome & Edge Browsers)"),
    ("Core Architecture:", "React.js + Syncfusion + Dexie.js (Offline) + Socket.IO + Fastify (Node.js) + PostgreSQL (UUIDv7)"),
    ("Offline & Sync Strategy:", "100% Offline CRUD via IndexedDB, Outbox Sync Queue & Optimistic Concurrency Control"),
    ("Keyboard & UI Design:", "100% Keyboard-First (Tally-Style Enter-to-Next, F1-F12 Hotkeys), Master HOC & Reusable <ERP.../> UI Library"),
    ("Target Audience:", "Executive Management, Senior Technical Leads, System Architects & Core Development Team"),
    ("Document Status:", "FINAL PRODUCTION SPECIFICATION (Ready for Execution)")
]
for i, (k, v) in enumerate(meta_data):
    ck = meta_tbl.cell(i, 0)
    cv = meta_tbl.cell(i, 1)
    ck.width = Inches(1.8)
    cv.width = Inches(4.7)
    
    pk = ck.paragraphs[0]
    pk.paragraph_format.space_after = Pt(2)
    rk = pk.add_run(k)
    rk.bold = True
    rk.font.size = Pt(9.5)
    rk.font.name = 'Calibri'
    rk.font.color.rgb = RGBColor(31, 78, 121)
    
    pv = cv.paragraphs[0]
    pv.paragraph_format.space_after = Pt(2)
    rv = pv.add_run(v)
    rv.font.size = Pt(9.5)
    rv.font.name = 'Calibri'

doc.add_page_break()

# =========================================================================
# SECTION 1: EXECUTIVE SUMMARY & ARCHITECTURAL FOUNDATIONS
# =========================================================================
add_heading_1("1. Executive Summary & Core Architectural Decisions")

doc.add_paragraph(
    "The Bansagar Fisheries ERP is a mission-critical, enterprise-grade supply chain and commercial management platform "
    "specifically designed for high-volume commercial fish processing depots. It governs the end-to-end operational lifecycle: "
    "from reservoir point harvest arrival and quality sorting, through prioritized digital weighing tolls, 1-click spot sales & "
    "thermal barcode packaging, crushed ice cold storage, wholesale intercity fleet dispatches (Delhi, Mumbai, Kolkata), "
    "and complex fortnightly fishermen wage payroll & liability settlements."
)

add_callout(
    doc,
    "The 6 Core Architectural Pillars",
    "1. Pure Web-Based Private Admin: Runs in desktop/laptop browsers (Chrome/Edge) with zero client installation, connected to local depot intranet or private cloud.\n"
    "2. 100% Offline Autonomy (Dexie.js IndexedDB): Operators can create, edit, update, and delete records seamlessly with ZERO internet connectivity.\n"
    "3. Real-Time Multi-Terminal Synchronization (Socket.IO + Fastify): Instantaneous (~15ms) data reflection across all depot screens with an Active/Deactive Live Sync Switch.\n"
    "4. Sequential UUIDv7 Primary Keys: High-speed B-Tree indexing without fragmentation, enabling collision-free client-side ID generation.\n"
    "5. 100% Keyboard-Driven UX: Tally ERP style Enter-to-Next traversal and F1-F12 hotkeys for wet-floor gloves-on operation without a mouse.\n"
    "6. Master HOC & Reusable UI Component Library: Centralized withERPPage HOC and custom <ERP.../> components ensuring single-point maintenance."
)

# Tech Stack Table
add_heading_2("1.1 Approved Technology Stack Matrix")
tech_table_data = [
    ("Frontend Framework", "React 18.3+ / 19 (TypeScript) + Vite", "Virtual DOM diffing, focus retention during live updates, fast HMR."),
    ("Enterprise UI Suite", "Syncfusion Essential JS 2 (@syncfusion/ej2-react-*)", "Enterprise DataGrid, Excel/PDF export, Aggregates, Charts, Dropdowns, DatePickers."),
    ("Client-Side Database", "Dexie.js (Browser IndexedDB)", "Zero-latency local CRUD (<1ms), reactive live queries (useLiveQuery), sync queue outbox."),
    ("Real-Time Engine", "Socket.IO (Client & Server v4.7+)", "Low-latency bi-directional WebSocket broadcast across all depot laptops and screens."),
    ("Hardware Interface", "Web Serial API (navigator.serial)", "Direct RS-232 / USB COM port digital weighing scale connectivity directly in browser."),
    ("Backend Runtime", "Node.js v20+ LTS + Fastify Framework", "Ultra-high throughput (~75,000 req/sec, ~1.5ms latency), compiled AJV schema validation."),
    ("Database Engine", "PostgreSQL 16+ / 17", "ACID compliance, sequential UUIDv7 primary keys, and change-data-capture triggers."),
    ("ORM & Data Access", "Prisma ORM (v5.18+)", "Type-safe migrations, connection pooling, and atomic multi-table transactions.")
]

t_table = doc.add_table(rows=len(tech_table_data) + 1, cols=3)
t_table.alignment = WD_TABLE_ALIGNMENT.CENTER
widths = [1.6, 2.3, 2.6]
style_table_header(t_table.rows[0], widths)
t_table.rows[0].cells[0].paragraphs[0].text = "Layer / Component"
t_table.rows[0].cells[1].paragraphs[0].text = "Approved Technology"
t_table.rows[0].cells[2].paragraphs[0].text = "Architectural Justification"

for idx, (c1, c2, c3) in enumerate(tech_table_data):
    row = t_table.rows[idx + 1]
    style_table_row(row, widths, is_even=(idx % 2 == 1))
    row.cells[0].paragraphs[0].text = c1
    row.cells[1].paragraphs[0].text = c2
    row.cells[2].paragraphs[0].text = c3

doc.add_page_break()

# =========================================================================
# SECTION 2: MASTER HOC & CUSTOM UI COMPONENT ARCHITECTURE
# =========================================================================
add_heading_1("2. Master HOC & Custom UI Component Architecture")

doc.add_paragraph(
    "To ensure future upgrades, styling modifications, permission changes, and hotkey adjustments can be executed from "
    "ONE SINGLE PLACE without modifying dozens of page files, the application is strictly architected around a Master Higher-Order "
    "Component (withERPPage) and a unified Custom Component Library."
)

add_heading_2("2.1 The Master HOC (withERPPage.tsx) Responsibilities")
add_bullet("Automatically checks user role permissions for the specific module; renders clean 'Access Denied' if unauthorized.", "1. RBAC Guard: ")
add_bullet("Registers F1-F12 hotkeys, Escape-to-close, Ctrl+S to save, and Tally-style Enter-key traversal across all form fields.", "2. Universal Hotkeys: ")
add_bullet("Displays live green/red connection status and pending offline outbox count (Dexie sync_queue).", "3. Offline Status Pill: ")
add_bullet("Contains the Syncfusion SwitchComponent allowing operators to freeze/unfreeze live screen re-renders on demand.", "4. Live Sync Gate: ")
add_bullet("Automatically connects to Web Serial API digital scales on weighing pages and disconnects cleanly on unmount.", "5. Hardware Manager: ")
add_bullet("Logs user email, module name, and access timestamp to central audit trail.", "6. Activity Audit Logger: ")

add_heading_2("2.2 Reusable Atomic UI Component Library (src/components/ui/)")
comp_data = [
    ("<ERPButton />", "Wraps Syncfusion Button", "Built-in permission checking (auto-hides if unauthorized), hotkey label badge, loading spinner."),
    ("<ERPNumericInput />", "Wraps Syncfusion NumericTextBox", "Configured for 3-decimal precision fish weights (0.000 Kg) or currency (₹), auto Enter-to-next focus."),
    ("<ERPTextInput />", "Wraps Syncfusion TextBox", "Floating labels, validation error text, uppercase/trim formatters, Enter-key traversal."),
    ("<ERPDropdown />", "Wraps Syncfusion DropDownList", "Built-in search filter, key-value data source mapping, keyboard selection support."),
    ("<ERPAutoComplete />", "Wraps Syncfusion AutoComplete", "Fast fuzzy search for Fishermen (Code/Name/Samiti) and Species variants."),
    ("<ERPDatePicker />", "Wraps Syncfusion DatePicker", "Standardized Indian date formatting (DD/MM/YYYY) with keyboard date entry."),
    ("<ERPDataGrid />", "Wraps Syncfusion DataGrid", "Pre-configured Excel/PDF export, total summary aggregates, pagination, and live row injection."),
    ("<ERPDialog />", "Wraps Syncfusion Dialog", "Standardized confirmation modals with Esc-to-close and Enter-to-confirm."),
    ("<ERPSwitch />", "Wraps Syncfusion Switch", "Enterprise toggle for Active/Deactive Live Sync and status flags."),
    ("<ERPCard />", "Standard Container Card", "Unified white box shadow container with header actions and KPI metrics.")
]

c_table = doc.add_table(rows=len(comp_data) + 1, cols=3)
c_table.alignment = WD_TABLE_ALIGNMENT.CENTER
c_widths = [1.6, 2.0, 2.9]
style_table_header(c_table.rows[0], c_widths)
c_table.rows[0].cells[0].paragraphs[0].text = "Custom Component"
c_table.rows[0].cells[1].paragraphs[0].text = "Underlying Syncfusion Package"
c_table.rows[0].cells[2].paragraphs[0].text = "Built-in Capabilities"

for idx, (c1, c2, c3) in enumerate(comp_data):
    row = c_table.rows[idx + 1]
    style_table_row(row, c_widths, is_even=(idx % 2 == 1))
    row.cells[0].paragraphs[0].text = c1
    row.cells[1].paragraphs[0].text = c2
    row.cells[2].paragraphs[0].text = c3

doc.add_page_break()

# =========================================================================
# SECTION 3: COMPLETE 11-MODULE OPERATIONAL SPECIFICATION
# =========================================================================
add_heading_1("3. Complete 11-Module Operational & Functional Specification")

modules_info = [
    ("MENU 1: DASHBOARD & LIVE RADAR", [
        ("1.1 Live Depot Floor Overview", "Real-time visual radar: Fish Arrived Today (Kg), Crates Sorted, Spot Sales (₹), Boxes Ready, Cold Room Stock, and Spoilage % with 1-click shortcut buttons."),
        ("1.2 Intake & Quality Yield Analytics", "Species breakdown pie charts and catching point quality ratings comparing Fresh vs Rotten vs Spoiled percentages."),
        ("1.3 Intercity Dispatch Tracker", "Live map and transit cards showing vehicles en route to Delhi, Kolkata, and Mumbai with driver direct call button.")
    ]),
    ("MENU 2: MASTER CONFIGURATIONS (13 MASTERS)", [
        ("2.1 Catching Points & Ghats", "Registers Point Code, Name (Ghat A, Deolond, Markandeya), Transport Mode, Distance to Depot (Km), and Supervisor."),
        ("2.2 Processing Depots Master", "Central depots, cold storage room capacities, scale device IDs, and thermal printer endpoints."),
        ("2.3 Destination Cities & Markets", "Intercity markets (Delhi Gazipur, Kolkata Howrah, Mumbai Sassoon Dock), route distance, and default buyers."),
        ("2.4 Species & 3-Tier Quality Catalog", "Species catalog (Rohu, Catla, Mrigal, Kalbasu, Aor, Sawal, Lanchi, Minor) and 3 Quality Tiers (Fresh, Rotten, Spoiled)."),
        ("2.5 Packaging & Standard Tare", "Standard crate capacity (35-40 Kg), crate tare weight (2.5 Kg), box dimensions, and crushed ice allowances."),
        ("2.6 Customer & Buyer Directory", "Local spot buyers and intercity wholesale commission agents with credit limits and GSTIN details."),
        ("2.7 Fleet & Transport Vehicles", "Reefer trucks, pickups, tractors, carrying capacity, GPS device IDs, and road permit dates."),
        ("2.8 Drivers Directory", "Authorized drivers roster with driving license numbers, mobile contacts, and emergency details."),
        ("2.9 Group Types & Govt Charges", "Traditional vs Commercial group classifications and government revenue deduction percentages."),
        ("2.10 Main Groups (Samitis)", "Fisheries Cooperative Societies, registered registration numbers, bank accounts, and IFSC codes."),
        ("2.11 Fishermen Directory", "Fisherman Code, Name, Samiti link, Bank Account, IFSC, Major/Minor/Sawal rates, and opening liability balance."),
        ("2.12 Boats & Vessels (Nav)", "Catching and transport boats operating on Bansagar reservoir."),
        ("2.13 Store Gear & Nets (Jaal)", "Nets, jaal types, twine, floats, packaging crates, and ice procurement master.")
    ]),
    ("MENU 3: DEPOT INTAKE, QUALITY SORTING & TOLL", [
        ("3.1 Point Inward Arrival Manifest", "Gate entry logging vehicle type, estimated crates, and staging bay assignment."),
        ("3.2 3-Tier Quality Sorting Screen", "Floor labor quality separation into Fresh (Prime), Rotten (Scrap), and Spoiled (Discounted) lots."),
        ("3.3 Live Prioritized Toll Screen (Rules A-E)", "8-Species digital weighing scale matrix executing Priority Rules A to E with 1-click toll ticket printing."),
        ("3.4 Bachat Toll (Mix Leftover Toll)", "Reconciliation of odd leftover crates (<35 kg) pooled across all catching points."),
        ("3.5 Intake History & Slips Archive", "Searchable archive of historical toll tickets with reprint and PDF export capabilities."),
        ("3.6 Change Toll Rates Matrix", "Master matrix for updating toll purchase rates with effective date versioning.")
    ]),
    ("MENU 4: 1-CLICK SCALE POS & BOX PACKAGING", [
        ("4.1 1-Click Scale POS & Packaging", "Unified weighing floor screen executing Weighing + Spot Invoicing OR Box Packaging in ONE click."),
        ("4.2 Spot Sales Counter", "Cash/UPI retail sales counter managing invoice collection and daily cash drawer reconciliation."),
        ("4.3 Thermal Box Label Printing", "High-speed adhesive label printing (Box #, Species, Grade, Net Wt, Date/Time) for Zebra/TSC printers."),
        ("4.4 Ready Box Packaging Inventory", "Live floor inventory of sealed boxes waiting for truck loading grouped by species and grade.")
    ]),
    ("MENU 5: ICE PRESERVATION & COLD STORAGE", [
        ("5.1 Floor Sweep & Closing Production", "End-of-shift floor clearance recording unsold loose fish preventing inventory shrinkage."),
        ("5.2 Crushed Ice Crates Sealing", "Logs crates packed with crushed ice, fish-to-ice ratio, and sealing tag numbers for overnight preservation."),
        ("5.3 Cold Room Bay Management", "Tracks crate batches in cold storage bays with temperature monitoring and expiration alerts."),
        ("5.4 Daily Mass Balance Reconciliation", "Validates: Inward Catch = Spot Sales + Boxed Dispatches + Cold Stock + Scrap (Flags discrepancy > 0.5%).")
    ]),
    ("MENU 6: INTERCITY DISPATCH & FLEET LOGISTICS", [
        ("6.1 Shipment Consignment Builder", "Multi-box truck consignment builder selecting destination city, consignee, and prepared boxes."),
        ("6.2 Trip Sheet & Driver Assignment", "Assigns truck, driver, departure time, route distance (Km), and advance diesel/freight cash."),
        ("6.3 Barcode Loading Scanner Verification", "Wireless barcode verification at loading dock preventing wrong-box or wrong-destination loading."),
        ("6.4 Gate Pass & Delivery Challan", "Generates official 3-copy Delivery Challan, Highway Gate Pass, and Consignment Notes."),
        ("6.5 In-Transit Live Tracking & POD", "Monitors vehicle en route, destination delivery acknowledgment, received weight, and transit shrinkage.")
    ]),
    ("MENU 7: COMMERCIAL BILLING & CUSTOMER ACCOUNTS", [
        ("7.1 Spot Invoices & Cash Register", "Manages counter sales invoices, cash drawer collections, and daily cash closing balance."),
        ("7.2 Wholesale Consignment Invoices", "Generates commercial tax invoices for destination buyers based on manifest box weights and agreed rates."),
        ("7.3 Customer Credit Ledgers", "Itemized buyer statements showing Billed Invoices, Payments, Claims, and Outstanding Aging."),
        ("7.4 Payment Receipts (NEFT/UPI/Cash)", "Records buyer remittances against pending invoices with bank UTR reference numbers."),
        ("7.5 Transit Damage & Shrinkage Claims", "Processes destination melting claims (1-2% tolerance) and issues Credit Notes."),
        ("7.6 Cash Deposited Collection", "Records direct cash collections and deposits from fishermen and traders.")
    ]),
    ("MENU 8: FISHERMEN & SAMITI PAYROLL (WAGES)", [
        ("8.1 Wages Sheet Generator", "Fortnightly payroll engine: (Catch Wt × Rate Slabs) - Govt Charges - Advances - Liability Deductions."),
        ("8.2 Fisherman Cash Advances", "Registers loan cash advances given to individual fishermen."),
        ("8.3 Bulk Advance Wages", "Bulk entry screen for distributing advances across an entire Samiti roster."),
        ("8.4 Samiti Advance Wages", "Registers society-level seasonal advances."),
        ("8.5 Samiti Consolidated Wages Sheet", "Summary payroll statement for cooperative society executive committees."),
        ("8.6 Transferred Liability Management", "Transfers loan liabilities from old groups/fishermen to new accounts."),
        ("8.7 Group Liability Deductions", "Automates recovery deductions from fortnightly catch earnings."),
        ("8.8 Bank Payout Export Sheet", "Direct export of Net Payable amounts to Fisherman Bank Accounts (Account No, IFSC, Amount).")
    ]),
    ("MENU 9: STORE & GEAR INVENTORY (STOCKS)", [
        ("9.1 Store Inward", "Purchase entry for nets (Jaal), twine, ice blocks, plastic crates, and packaging materials."),
        ("9.2 Store Inward Returns", "Returns of damaged raw materials to suppliers."),
        ("9.3 Store Outward", "Issuance of gear, nets, and ice to fishermen and depot boats with ledger debiting."),
        ("9.4 Store Outward Returns", "Return of unused or repaired gear back to central depot storage.")
    ]),
    ("MENU 10: EXECUTIVE INTELLIGENCE & AUDIT REPORTS", [
        ("10.1 Production Yield Report", "Detailed daily, monthly, and yearly species catch volumes across all points."),
        ("10.2 Samiti-wise Production Report", "Breakdown of harvested fish volume per cooperative society."),
        ("10.3 Inactive Fishermen Register", "Identifies registered fishermen with zero catch activity over selectable date ranges."),
        ("10.4 Fish Percentage Analysis", "Distribution percentage of Major Carp vs Minor vs Catfish across reservoir zones."),
        ("10.5 Fisherman Ledger Statement", "Complete financial ledger statement (Catch Earnings vs Advances vs Net Payouts)."),
        ("10.6 Govt P1 & P2 Regulatory Reports", "Official statutory production reports required by Fisheries Department."),
        ("10.7 Outward Sales Realization", "Dispatches revenue realization across wholesale markets."),
        ("10.8 Outward Ledger Report", "Consolidated sales ledger per buyer and destination city."),
        ("10.9 Wages Audit Reports", "Monthly, quarterly, and half-yearly wages and advance recovery audits."),
        ("10.10 City Profitability Report", "Compares net realization per kg across Delhi, Kolkata, and Mumbai after ice/freight costs."),
        ("10.11 Box Barcode Traceability", "Scan any box barcode to see: Origin Point, Catch Date, Pack Time, Truck #, Driver, and Buyer."),
        ("10.12 Fleet Transit Performance", "Turnaround times, diesel efficiency, delay hours, and shrinkage % per vehicle/driver.")
    ]),
    ("MENU 11: SYSTEM ADMINISTRATION, SECURITY & TOOLS", [
        ("11.1 Administrator Users", "User accounts, profile pictures, and depot assignments."),
        ("11.2 Administrator Roles / Groups", "Role definitions: Super Admin, Depot Manager, Weighing Clerk, Cashier, Dispatch In-charge."),
        ("11.3 7-Action Dynamic Permission Matrix", "Visual matrix mapping View, Add, Edit, Delete, Export, Print, Lock permissions to menus."),
        ("11.4 Hardware Peripheral Settings", "COM Port baud rates for digital scales and IP/Bluetooth thermal printer endpoints."),
        ("11.5 Period-End Financial Audit Lock", "Enforces immutable cutoff dates strictly blocking historical data modifications."),
        ("11.6 Closed Dates Register", "Log of all past financial closed/locked periods."),
        ("11.7 Application Global Settings", "System-wide constants, company branding, and default parameters."),
        ("11.8 Audit Activity Trail Logs", "Immutable log recording every user action with timestamp and IP address."),
        ("11.9 Trash Recovery Center", "Soft-delete trash bin for restoring accidentally deleted records."),
        ("11.10 Bulk Actions Controller", "Bulk activation, deactivation, and batch locking."),
        ("11.11 Multi-Year Database Tools", "Yearly database archiving, closing balance forwarding, and database exports."),
        ("11.12 Excel Import Center", "Batch import of legacy daily tolls, production sheets, and dispatches.")
    ])
]

for m_title, sublist in modules_info:
    add_heading_2(m_title)
    for s_title, s_desc in sublist:
        add_bullet(s_desc, f"{s_title}: ")

doc.add_page_break()

# =========================================================================
# SECTION 4: COMPLETE PRODUCTION FILE STRUCTURE
# =========================================================================
add_heading_1("4. Complete Production File Structure")

doc.add_paragraph(
    "Here is the complete, granular file structure representing every single component, route, hook, database schema, "
    "and utility in the codebase:"
)

file_structure_text = """
bansagar-fishery-erp/
├── backend/                                   # FASTIFY + PRISMA + TYPESCRIPT BACKEND
│   ├── package.json
│   ├── tsconfig.json
│   ├── prisma/
│   │   ├── schema.prisma                      # PostgreSQL Schema (UUIDv7, Auditing, Sync)
│   │   └── migrations/                        # Versioned SQL migrations
│   └── src/
│       ├── config/ (env.ts, constants.ts)
│       ├── db/ (prismaClient.ts)
│       ├── middleware/ (authGuard.ts, permissionGuard.ts, errorHandler.ts)
│       ├── websocket/ (socketServer.ts, broadcasters.ts)
│       ├── sync/ (syncRoutes.ts, syncController.ts, conflictEngine.ts)
│       ├── modules/
│       │   ├── auth/ (authRoutes.ts, authService.ts)
│       │   ├── dashboard/ (dashboardRoutes.ts, dashboardService.ts)
│       │   ├── masters/ (groupTypeRoutes.ts, samitiRoutes.ts, fishermanRoutes.ts, pointRoutes.ts, depotRoutes.ts, marketRoutes.ts, speciesRoutes.ts, packagingRoutes.ts, customerRoutes.ts, vehicleRoutes.ts, driverRoutes.ts, boatRoutes.ts, gearRoutes.ts)
│       │   ├── intake/ (arrivalManifestRoutes.ts, qualitySortingRoutes.ts, dailyTollRoutes.ts, bachatTollRoutes.ts, tollHistoryRoutes.ts, rateRevisionRoutes.ts)
│       │   ├── pos/ (scalePosRoutes.ts, spotCounterRoutes.ts, barcodeLabelRoutes.ts, boxInventoryRoutes.ts)
│       │   ├── coldstorage/ (closingTollRoutes.ts, iceSealingRoutes.ts, bayManagementRoutes.ts, massBalanceRoutes.ts)
│       │   ├── dispatch/ (shipmentRoutes.ts, tripSheetRoutes.ts, loadingVerifyRoutes.ts, challanRoutes.ts, trackingRoutes.ts)
│       │   ├── billing/ (spotInvoiceRoutes.ts, wholesaleInvoiceRoutes.ts, customerLedgerRoutes.ts, paymentReceiptRoutes.ts, shrinkageClaimRoutes.ts, cashDepositRoutes.ts)
│       │   ├── wages/ (wagesSheetRoutes.ts, advanceRoutes.ts, bulkAdvanceRoutes.ts, samitiAdvanceRoutes.ts, samitiWagesRoutes.ts, liabilityRoutes.ts, bankExportRoutes.ts)
│       │   ├── stocks/ (storeInwardRoutes.ts, storeInwardReturnRoutes.ts, storeOutwardRoutes.ts, storeOutwardReturnRoutes.ts)
│       │   ├── reports/ (productionReportRoutes.ts, samitiProductionRoutes.ts, inactiveFishermanRoutes.ts, fishPercentRoutes.ts, fishermanLedgerRoutes.ts, p1p2ReportRoutes.ts, outwardSalesRoutes.ts, outwardLedgerRoutes.ts, wagesAuditRoutes.ts, cityProfitRoutes.ts, boxTraceabilityRoutes.ts, fleetPerformanceRoutes.ts)
│       │   └── administration/ (userRoutes.ts, roleRoutes.ts, permissionRoutes.ts, hardwareRoutes.ts, auditLockRoutes.ts, settingsRoutes.ts, auditLogRoutes.ts, trashRoutes.ts, bulkActionRoutes.ts, databaseToolsRoutes.ts, excelImportRoutes.ts)
│       └── server.ts
│
└── frontend/                                  # REACT + VITE + TYPESCRIPT + SYNCFUSION
    ├── package.json
    ├── tsconfig.json
    ├── vite.config.ts
    └── src/
        ├── config/ (apiConfig.ts, hotkeysConfig.ts)
        ├── context/ (AuthContext.tsx, LiveSyncContext.tsx)
        ├── db/ (localDb.ts [Dexie.js Schema], syncQueue.ts)
        ├── hoc/ (withERPPage.tsx [Master HOC])
        ├── hooks/ (useAuth.ts, usePermissions.ts, useKeyboardShortcuts.ts, useEnterKeyFocus.ts, useRealtimeSync.ts)
        ├── services/ (apiClient.ts, socketService.ts, syncWorker.ts, scaleService.ts, printerService.ts)
        ├── components/
        │   ├── ui/ (ERPButton.tsx, ERPTextInput.tsx, ERPNumericInput.tsx, ERPDropdown.tsx, ERPAutoComplete.tsx, ERPDatePicker.tsx, ERPDataGrid.tsx, ERPDialog.tsx, ERPSwitch.tsx, ERPCard.tsx, index.ts)
        │   ├── layout/ (ERPHeader.tsx, ERPSidebar.tsx, Breadcrumbs.tsx, HotkeyLegendBar.tsx, MainLayout.tsx)
        │   └── auth/ (PermissionGuard.tsx)
        ├── pages/
        │   ├── Auth/ (LoginPage.tsx, EditProfilePage.tsx, ChangePasswordPage.tsx, ForgotPasswordPage.tsx)
        │   ├── Dashboard/ (LiveFloorOverviewPage.tsx, YieldAnalyticsPage.tsx, DispatchTrackerPage.tsx)
        │   ├── Masters/ (FishingPointsPage.tsx, ProcessingDepotsPage.tsx, DestinationMarketsPage.tsx, SpeciesCatalogPage.tsx, PackagingTarePage.tsx, CustomersBuyersPage.tsx, FleetVehiclesPage.tsx, DriversDirectoryPage.tsx, GroupTypesPage.tsx, SamitisMainGroupPage.tsx, FishermenDirectoryPage.tsx, BoatsNavMasterPage.tsx, StoreGearJaalPage.tsx)
        │   ├── DailyToll/ (PointArrivalManifestPage.tsx, QualitySortingPage.tsx, LiveWeighingFloorPage.tsx, BachatTollPage.tsx, IntakeSlipsArchivePage.tsx, ChangeTollRatePage.tsx)
        │   ├── SpotPOS/ (OneClickScalePOSPage.tsx, SpotSalesCounterPage.tsx, ThermalLabelQueuePage.tsx, ReadyBoxInventoryPage.tsx)
        │   ├── ColdStorage/ (FloorSweepClosingPage.tsx, IceCratesSealingPage.tsx, BayManagementPage.tsx, MassBalanceReconciliationPage.tsx)
        │   ├── Dispatch/ (CreateShipmentPage.tsx, TripSheetAssignmentPage.tsx, BarcodeLoadingVerifyPage.tsx, GatePassChallanPage.tsx, TransitTrackingPage.tsx)
        │   ├── Billing/ (SpotInvoicesCashRegisterPage.tsx, WholesaleInvoicesPage.tsx, CustomerLedgersPage.tsx, PaymentReceiptsPage.tsx, ShrinkageClaimsPage.tsx, CashDepositedPage.tsx)
        │   ├── Wages/ (WagesSheetPage.tsx, FishermanAdvancesPage.tsx, BulkAdvanceWagesPage.tsx, SamitiAdvanceWagesPage.tsx, SamitiWagesSheetPage.tsx, TransferredLiabilityPage.tsx, LiabilityDeductionsPage.tsx, BankPayoutExportPage.tsx)
        │   ├── Stocks/ (StoreInwardPage.tsx, StoreInwardReturnPage.tsx, StoreOutwardPage.tsx, StoreOutwardReturnPage.tsx)
        │   ├── Reports/ (ProductionYieldReportPage.tsx, MassProductionReconciliationReportPage.tsx, SamitiProductionReportPage.tsx, InactiveFishermenReportPage.tsx, FishPercentAnalysisReportPage.tsx, FishermanLedgerStatementPage.tsx, P1P2RegulatoryReportPage.tsx, OutwardDispatchSalesReportPage.tsx, OutwardLedgerReportPage.tsx, WagesAuditReportsPage.tsx, CityProfitabilityReportPage.tsx, BoxTraceabilityReportPage.tsx, FleetPerformanceReportPage.tsx)
        │   └── Administration/ (UserManagementPage.tsx, AdministratorRolesPage.tsx, PermissionMatrixPage.tsx, HardwareSettingsPage.tsx, PeriodAuditLockPage.tsx, ClosedDatesRegisterPage.tsx, ApplicationSettingsPage.tsx, AuditLogsPage.tsx, TrashRecoveryPage.tsx, BulkActionsPage.tsx, DatabaseToolsPage.tsx, ExcelImportCenterPage.tsx)
        ├── utils/ (keyboardFocus.ts, formatters.ts, excelExporter.ts)
        ├── routes/ (AppRoutes.tsx, ProtectedRoute.tsx)
        ├── App.tsx
        └── main.tsx
"""

p_box = doc.add_paragraph()
r_box = p_box.add_run(file_structure_text)
r_box.font.name = 'Consolas'
r_box.font.size = Pt(8)
r_box.font.color.rgb = RGBColor(30, 41, 59)

doc.add_page_break()

# =========================================================================
# SECTION 5: 10 CRITICAL ENTERPRISE ENHANCEMENTS
# =========================================================================
add_heading_1("5. 10 Critical Enterprise Enhancements for Depot Excellence")

enhancements = [
    ("1. Digital Scale Weight Stability Lock", "Prevents moving fish bouncing fraud by sampling 3 consecutive readings within ±50g before enabling the Enter/Save button."),
    ("2. Silent Kiosk Printing (No Popups)", "Configures Chrome/Edge with --kiosk --kiosk-printing for instant 0.1s thermal slip/barcode label printing without print dialogs."),
    ("3. Tiered Client Data Retention Policy", "Stores 100% Master Data + active season in local IndexedDB (Dexie), while querying multi-year historical ledgers on-demand from PostgreSQL."),
    ("4. Real-Time Spoilage Alert Engine", "Triggers high-priority manager alerts if any catching point exceeds 3.0% rotten fish in arriving batches."),
    ("5. Numpad Tare Presets", "Single-key shortcuts (Numpad 1 for Blue Crate 2.5kg, Numpad 2 for Black Tub 3.8kg, Numpad 0 for Zero Tare) for maximum floor speed."),
    ("6. Interstate e-Way Bill & GST Compliance", "1-click generation of official NIC e-Way Bill JSON (HSN 0302) for Delhi, Kolkata, and Mumbai wholesale highway transit checkpoints."),
    ("7. Audio-Visual Barcode Loading Dock Guard", "Handheld scanner plays loud buzzer and flashes red if a box meant for Kolkata is accidentally loaded into a Delhi truck."),
    ("8. Time-Sync & Clock Skew Protection", "Calculates server time offset on client boot, guaranteeing 100% accurate UUIDv7 timestamps even with dead laptop CMOS batteries."),
    ("9. Local Depot LAN Master-Node Hybrid Model", "Runs Fastify + PostgreSQL on a local depot office master PC; all 4 floor laptops operate at 0.5ms latency with 0 days of internet, auto-syncing to cloud when broadband returns."),
    ("10. Automated Zero-Downtime S3 Cloud Backups", "Nightly automated pg_dump with encrypted cloud upload to AWS S3/Google Cloud and 1-click disaster recovery.")
]

for title, desc in enhancements:
    add_bullet(desc, f"{title}: ")

# Output Path
output_file_path = os.path.join("d:\\bansagar", "Bansagar_Fisheries_Final_Technical_Architecture_and_Specification.docx")
doc.save(output_file_path)
print(f"Successfully generated: {output_file_path}")
