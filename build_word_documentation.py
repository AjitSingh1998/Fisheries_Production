import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
import os

doc = docx.Document()

# Set standard page margins (1 inch)
for section in doc.sections:
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)

# Helper function to style table headers
def style_table_header(row, col_widths, bg_color="1F4E79", font_color="FFFFFF"):
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

# Helper function to style table rows
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

# Helper for Callout Box
def add_callout(doc, title, text, bg_color="EBF3FA", border_color="2563EB"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    cell.width = Inches(6.5)
    
    # Border & background
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{bg_color}"/>')
    tcPr.append(shd)
    
    # Left thick border
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
    
    doc.add_paragraph().paragraph_format.space_after = Pt(6)

# =========================================================================
# COVER / TITLE SECTION
# =========================================================================
title_p = doc.add_paragraph()
title_p.paragraph_format.space_before = Pt(20)
title_p.paragraph_format.space_after = Pt(4)
r_main_title = title_p.add_run("BANSAGAR RESERVOIR FISHERIES ERP")
r_main_title.font.name = 'Calibri'
r_main_title.font.size = Pt(24)
r_main_title.font.bold = True
r_main_title.font.color.rgb = RGBColor(31, 78, 121)

sub_p = doc.add_paragraph()
sub_p.paragraph_format.space_after = Pt(16)
r_sub = sub_p.add_run("Complete Module-by-Module Operational Guide, Business Logic, and System Architecture Specification")
r_sub.font.name = 'Calibri'
r_sub.font.size = Pt(13)
r_sub.font.italic = True
r_sub.font.color.rgb = RGBColor(89, 89, 89)

# Metadata Box
meta_tbl = doc.add_table(rows=5, cols=2)
meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
meta_data = [
    ("Document Type:", "Comprehensive Operational & Meeting Briefing Document"),
    ("Target Audience:", "Executive Board, Chief Accountants, Project Managers, Engineering Team"),
    ("Core Technology:", "PHP 7.4 / 8.2, CodeIgniter 3 HMVC, MySQL 5.7, Next.js 16 / React 19 ERP"),
    ("Scope Covered:", "All 11 Modules (Setup, Daily Toll, Stocks, Wages, Banking, Reports, Audit Lock)"),
    ("Status & Version:", "Production Verified / ERP 2.0 Architectural Baseline")
]
for i, (k, v) in enumerate(meta_data):
    cell_k = meta_tbl.cell(i, 0)
    cell_v = meta_tbl.cell(i, 1)
    cell_k.width = Inches(1.8)
    cell_v.width = Inches(4.7)
    
    pk = cell_k.paragraphs[0]
    pk.paragraph_format.space_after = Pt(2)
    rk = pk.add_run(k)
    rk.bold = True
    rk.font.size = Pt(9.5)
    rk.font.name = 'Calibri'
    rk.font.color.rgb = RGBColor(31, 78, 121)
    
    pv = cell_v.paragraphs[0]
    pv.paragraph_format.space_after = Pt(2)
    rv = pv.add_run(v)
    rv.font.size = Pt(9.5)
    rv.font.name = 'Calibri'

doc.add_page_break()

# =========================================================================
# 1. EXECUTIVE SUMMARY & SYSTEM AIM
# =========================================================================
h1 = doc.add_heading("1. Executive Summary & System Aim", level=1)
h1.paragraph_format.space_before = Pt(12)
h1.paragraph_format.space_after = Pt(6)

doc.add_paragraph(
    "The Bansagar Fisheries ERP is a specialized cooperative resource planning and multi-station financial accounting system "
    "developed for the Bansagar Reservoir project. The reservoir spans thousands of hectares and involves hundreds of registered "
    "fishermen organized under cooperative societies (Samitis). The system provides full digitization of operational and financial "
    "workflows, replacing manual ledger registers with automated, transparent, and auditable digital processes."
)

add_callout(
    doc,
    "Primary Strategic Aims of the System",
    "1. Eliminate catch weight manipulation and calculation errors across remote weighing stations.\n"
    "2. Automate store gear credit (GLD) and seasonal cash advance (ALD) tracking per fisherman.\n"
    "3. Enforce dynamic multi-liability recovery during weekly/monthly wages settlement.\n"
    "4. Enable automated direct-to-bank salary disbursement via Axis Bank and ICICI Bank batch files.\n"
    "5. Automate statutory Government P1/P2 Fisheries Department registers and royalty remittances."
)

# =========================================================================
# 2. HMVC ARCHITECTURE & MODULE DIRECTORY
# =========================================================================
h2 = doc.add_heading("2. System Architecture & Module Directory", level=1)
h2.paragraph_format.space_before = Pt(12)
h2.paragraph_format.space_after = Pt(6)

doc.add_paragraph(
    "The application is architected around the Hierarchical Model-View-Controller (HMVC) modular pattern in CodeIgniter 3. "
    "Each operational unit is an isolated, self-contained module containing its own Controllers, Models, and Views:"
)

mod_table = doc.add_table(rows=1, cols=4)
mod_table.alignment = WD_TABLE_ALIGNMENT.CENTER
col_w = [1.2, 1.8, 1.5, 2.0]

headers = ["Module", "Directory Path", "Primary Controller", "Operational Responsibility"]
for idx, h in enumerate(headers):
    mod_table.cell(0, idx).paragraphs[0].text = h
style_table_header(mod_table.rows[0], col_w)

modules_list = [
    ("setup", "application/modules/setup", "Setup.php, Fishermen.php", "Master Data: Fishermen, Samiti Societies, Points, Rates, Products"),
    ("dailytoll", "application/modules/dailytoll", "Dailytoll.php", "Catch Intake: Scale weights, species calculations, royalty deductions"),
    ("stocks", "application/modules/stocks", "Stocks.php, Inward.php", "Warehouse Inventory: Vendor receipts, outward store credit issues (GLD)"),
    ("wages", "application/modules/wages", "Wages.php, Advance.php", "Payroll Engine: GLD/ALD recovery, Net Take-Home, Axis/ICICI bank batches"),
    ("reports", "application/modules/reports", "Reports.php, Production.php", "Reporting Suite: 18 standard reports including P1 & P2 Govt registers"),
    ("lock", "application/modules/lock", "Lock.php", "Audit Security: Period-end cutoff lock preventing retroactive tampering"),
    ("account", "application/modules/account", "Account.php", "Security & Auth: RBAC permissions, logins, password management"),
    ("dashboard", "application/modules/dashboard", "Dashboard.php", "Executive Analytics: Real-time volume trends, KPIs, and summaries")
]

for idx, (m, p, c, r) in enumerate(modules_list):
    row = mod_table.add_row()
    row.cells[0].paragraphs[0].text = m
    row.cells[1].paragraphs[0].text = p
    row.cells[2].paragraphs[0].text = c
    row.cells[3].paragraphs[0].text = r
    style_table_row(row, col_w, idx % 2 == 1)

doc.add_page_break()

# =========================================================================
# 3. MODULE 1: SETUP & MASTER DATA
# =========================================================================
h3 = doc.add_heading("3. Module 1: Setup / Master Data Management", level=1)
h3.paragraph_format.space_before = Pt(12)
h3.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Establish the authoritative master registry of all operational entities before transactions occur.\n"
    "• Enrolls fishermen and captures KYC + bank account details for automated batch payouts.\n"
    "• Groups fishermen under primary co-operative Samitis for collective commission and liability tracking.\n"
    "• Configures collection points (Ghats) and centralized procurement pricing per species."
)

sub_mods = [
    ("Fishermen Directory (`psac_fishermen`)", 
     "Enrolls reservoir fishermen. Stores Name, Father's Name, Unique Reservoir Code (Badge #), Bank Account Number, IFSC Code, and opening debt balances (Products Balance and Wages Balance). Enables automated bank disbursement without manual data re-entry."),
    ("Samiti Societies (`psac_main_groups`)", 
     "Represents Primary Fishermen Co-operative Societies (e.g., Kundan Samiti, Veer Samuh, Vindhyanchal Samiti). Maintains society commission percentages, bank credentials, and cumulative group debt."),
    ("Weighing Stations (`psac_fishing_points`)", 
     "Physical weighing scales stationed across Bansagar reservoir (e.g., Markandeya Point, Bansagar Main Point, Deolond Ghat). Used to audit station-wise intake volume."),
    ("Toll Wage Rates Master (`psac_rate_master`)", 
     "Configures species purchase rates per kg: Major Species (₹75.00/Kg), Local Minor (₹40.00/Kg), Sawal (₹50.00/Kg), and statutory Government royalty deduction (₹2.50/Kg). Applied automatically to all catch sessions."),
    ("Product Catalog (`psac_products`)", 
     "Maintains store inventory items issued on credit to crew (Nylon monofilament nets, twine, floats, sinkers, diesel, 2T engine oil, boat maintenance equipment)."),
    ("Liability Transfers (`psac_transfer_liability`)", 
     "Enables mutual debt reallocation between fishermen. If Crew Member A agrees to absorb Crew Member B's store debt, the system reallocates the liability with an immutable audit trail.")
]

for title, desc in sub_mods:
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(6)
    p.paragraph_format.space_after = Pt(2)
    r_t = p.add_run(f"• {title}: ")
    r_t.bold = True
    r_t.font.name = 'Calibri'
    r_t.font.color.rgb = RGBColor(31, 78, 121)
    r_d = p.add_run(desc)
    r_d.font.name = 'Calibri'

# =========================================================================
# 4. MODULE 2: DAILY CATCH INTAKE & WEIGHING MATRIX
# =========================================================================
h4 = doc.add_heading("4. Module 2: Daily Catch Intake & Weighing Matrix (Daily Toll)", level=1)
h4.paragraph_format.space_before = Pt(12)
h4.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Serve as the primary revenue and catch accounting engine across reservoir weighing stations.\n"
    "• Eliminates manual weighing slips and mathematical errors at the ghat.\n"
    "• Categorizes catches across 6 Major Carps, Local Minor, and Sawal species.\n"
    "• Automatically computes Gross Catch Earnings and deducts statutory Government Royalty (₹2.50/Kg).\n"
    "• Works offline at remote stations via Dexie IndexedDB and auto-syncs when online."
)

doc.add_heading("Species Categorization & Mathematical Calculations:", level=2)

formulas = [
    ("Total Major Weight (Tmwt)", "Sum of scale weights for Catla (Cwt), Rohu (Rwt), Mrigal (Mwt), Kalbasu (Kwt), Aor (Awt), and Lanchi (Lwt).\nFormula: Tmwt = Cwt + Rwt + Mwt + Kwt + Awt + Lwt"),
    ("Total Catch Weight (Twt)", "Total combined fish weight delivered by the fisherman in the session.\nFormula: Twt = Tmwt + Local_Minor + Swt"),
    ("Major Earnings Amount", "Earnings from Major Carps.\nFormula: Major_Amount = Tmwt * Major_Wage_Rate (₹75/Kg)"),
    ("Minor Earnings Amount", "Earnings from indigenous Minor fish.\nFormula: Minor_Amount = Local_Minor * Minor_Wage_Rate (₹40/Kg)"),
    ("Sawal Earnings Amount", "Earnings from Murrel / Sawal fish.\nFormula: Sawal_Amount = Swt * Sawal_Wage_Rate (₹50/Kg)"),
    ("Gross Catch Earnings", "Total raw earnings before deductions.\nFormula: Gross_Amount = Major_Amount + Minor_Amount + Sawal_Amount"),
    ("Statutory Govt Royalty Deduction", "Mandatory fee deducted per kg of total catch for MP Fisheries Department.\nFormula: Govt_Charges = Twt * Govt_Rate (₹2.50/Kg)"),
    ("Net Catch Wages", "Net liquid earnings credited to the fisherman's ledger for payroll settlement.\nFormula: Net_Amount = Gross_Amount - Govt_Charges")
]

for name, desc in formulas:
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(2)
    r_n = p.add_run(f"📐 {name}: ")
    r_n.bold = True
    r_n.font.color.rgb = RGBColor(31, 78, 121)
    p.add_run(desc)

doc.add_page_break()

# =========================================================================
# 5. MODULE 3: INVENTORY, STORES & CREDIT ISSUANCE
# =========================================================================
h5 = doc.add_heading("5. Module 3: Inventory, Stores & Credit System (Stocks)", level=1)
h5.paragraph_format.space_before = Pt(12)
h5.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Control warehouse fishing gear supplies and handle the store credit system.\n"
    "• Manages supplier purchases (Inward Challans) and maintains on-hand inventory.\n"
    "• Issues fishing nets, diesel, and equipment to fishermen on credit (Outward Challans).\n"
    "• Automatically links store credit issues directly to the fisherman's Group/Product Liability (GLD)."
)

doc.add_paragraph(
    "1. Inward Purchases (`stocks/inward`):\n"
    "   - Captures vendor shipments of nylon monofilament nets, floats, sinkers, diesel, and lubricants.\n"
    "   - Records supplier invoice numbers, unit costs, and quantities received, updating the live warehouse ledger.\n\n"
    "2. Outward Credit Issues (`stocks/outward`):\n"
    "   - Issues gear and fuel to fishermen without upfront cash payment.\n"
    "   - Generates official delivery challans.\n"
    "   - CRITICAL INTEGRATION: The Grand Total of the outward issue is immediately added to the fisherman's Group/Product Liability (GLD) for payroll recovery.\n\n"
    "3. Stock Valuation Ledger (`stocks/ledger`):\n"
    "   - Real-time audit of available stock vs. issued gear, tracking store assets and total inventory valuation."
)

# =========================================================================
# 6. MODULE 4: WAGES PAYROLL & LIABILITY SETTLEMENT
# =========================================================================
h6 = doc.add_heading("6. Module 4: Wages Payroll & Liability Settlement Engine", level=1)
h6.paragraph_format.space_before = Pt(12)
h6.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Reconcile catch earnings against debts and calculate net take-home pay.\n"
    "• Disburses seasonal cash advances (ALD) and logs direct cash repayments.\n"
    "• Reconciles catch earnings, store product liabilities (GLD), and advance liabilities (ALD).\n"
    "• Applies dynamic recovery caps so deductions never exceed net catch earnings.\n"
    "• Outputs net take-home pay for corporate bank batch processing."
)

doc.add_heading("Liability Accounting Architecture:", level=2)

doc.add_paragraph(
    "• Group / Product Liability (GLD):\n"
    "  GLD = Opening GLD + Total Outward Credit Purchases - Prior GLD Deductions - Direct Cash Repayments\n\n"
    "• Advance Wages Liability (ALD):\n"
    "  ALD = Opening ALD + Total Cash Advances Disbursed - Prior ALD Deductions - Direct Cash Repayments\n\n"
    "• Dynamic Settlement Recovery Logic:\n"
    "  1. Net Catch Earnings = Sum of Net Catch Wages across billing period.\n"
    "  2. GLD Deduction = min(Preset % or User Input, Current GLD Liability, Net Catch Earnings)\n"
    "  3. ALD Deduction = min(Preset % or User Input, Current ALD Liability, Net Catch Earnings - GLD Deduction)\n"
    "  4. Final Net Wages = Net Catch Earnings - (GLD Deduction + ALD Deduction)\n"
    "  5. Result: Fisherman receives net liquid pay; outstanding liabilities are reduced in the master ledger."
)

doc.add_page_break()

# =========================================================================
# 7. MODULE 5: CORPORATE BANKING BATCH EXPORTERS
# =========================================================================
h7 = doc.add_heading("7. Module 5: Corporate Banking Batch Exporters", level=1)
h7.paragraph_format.space_before = Pt(12)
h7.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Eliminate manual bank transfers and enable 1-click batch payroll execution.\n"
    "• Automatically maps settled final wages to RBI-compliant banking files.\n"
    "• Generates exact 13-Column Axis Bank batch files and 12-Column ICICI Bank CMS files.\n"
    "• Ensures zero data re-entry: Bank Name, Account Number, IFSC, and Net Amount are formatted for direct upload."
)

doc.add_heading("Axis Bank 13-Column Corporate Batch Layout:", level=2)
axis_tbl = doc.add_table(rows=1, cols=4)
axis_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
axis_w = [0.6, 2.2, 1.5, 2.2]
for idx, h in enumerate(["Col", "Field Name", "Sample Value", "Validation Rule"]):
    axis_tbl.cell(0, idx).paragraphs[0].text = h
style_table_header(axis_tbl.rows[0], axis_w)

axis_fields = [
    ("A", "Trans Type", "N", "Fixed 'N' for NEFT/RTGS"),
    ("B", "Payment Amount", "3225.00", "Final settled net wages amount"),
    ("C", "Value Date", "19/08/2026", "DD/MM/YYYY execution date"),
    ("D", "Beneficiary Name", "BADRIPRASAD ADKADE", "Fisherman Name as per bank records"),
    ("E", "Beneficiary Account Number", "3166010001234", "9-18 digit numeric bank account"),
    ("F", "Debit Email", "accounts@bansagarfishery.org", "Corporate notification email"),
    ("G", "Debit Mobile", "9876543210", "10-digit mobile number"),
    ("H", "Sender Name", "BANSAGAR RESERVOIR PROJECT", "Disbursing corporate entity name"),
    ("I", "Sender Account", "918020012345678", "Corporate debit current account"),
    ("J", "IFSC Code", "UTIB0001234", "11-character RBI IFSC code"),
    ("K", "Debit Account Type", "11", "Fixed '11' for Current Account"),
    ("L", "Narration", "WAGES 01/08 TO 07/08", "Billing cycle description"),
    ("M", "Client Reference", "BSG-WAG-2026-001", "Unique ERP Settlement Item ID")
]
for idx, (c, fn, sv, vr) in enumerate(axis_fields):
    r = axis_tbl.add_row()
    r.cells[0].paragraphs[0].text = c
    r.cells[1].paragraphs[0].text = fn
    r.cells[2].paragraphs[0].text = sv
    r.cells[3].paragraphs[0].text = vr
    style_table_row(r, axis_w, idx % 2 == 1)

doc.add_page_break()

# =========================================================================
# 8. MODULE 6: REPORTING SUITE & STATUTORY COMPLIANCE
# =========================================================================
h8 = doc.add_heading("8. Module 6: Reporting Suite & Statutory Compliance", level=1)
h8.paragraph_format.space_before = Pt(12)
h8.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Fulfill internal financial controls, society audits, and mandatory Government filings.\n"
    "• Generates 18 standard operational and executive reports.\n"
    "• Produces statutory P1 Government Registers and P2 Royalty Remittance Ledgers.\n"
    "• Analyzes biological species yield percentages across the reservoir."
)

rep_tbl = doc.add_table(rows=1, cols=3)
rep_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
rep_w = [1.8, 2.2, 2.5]
for idx, h in enumerate(["Category", "Report Name", "Key Deliverable / Purpose"]):
    rep_tbl.cell(0, idx).paragraphs[0].text = h
style_table_header(rep_tbl.rows[0], rep_w)

rep_list = [
    ("Production Reports", "1. Daily Production Report", "Station-wise daily catch weighings and operator audit"),
    ("Production Reports", "2. Monthly Production Matrix", "Day 1-31 matrix of catch per fisherman"),
    ("Production Reports", "3. Yearly Trend Comparison", "Month-by-month catch forecasting and species comparisons"),
    ("Production Reports", "4. Samiti Production Report", "Society-level volume benchmarking and commission basis"),
    ("Production Reports", "5. Inactive Crew Report", "Identifies absentee fishermen for membership review"),
    ("Outward Stock Reports", "6. Daily Outward Summary", "Daily warehouse release logs and challan audits"),
    ("Outward Stock Reports", "7. Monthly Outward Matrix", "Monthly gear consumption (Nets, Diesel) per crew"),
    ("Outward Stock Reports", "8. Fisherman Outward Ledger", "Itemized debit statement provided to fisherman"),
    ("Wages Reports", "9. Weekly Wages Statement", "Disbursement reconciliation against bank accounts"),
    ("Wages Reports", "10. Monthly Payroll Statement", "Monthly financial closure statement for accounting books"),
    ("Wages Reports", "11. Master Account Ledger", "Master bank transfer ledger for statutory audit"),
    ("Statutory Govt", "12. P1 Government Register", "MANDATORY REGISTER: Required by MP Fisheries Dept for lease compliance"),
    ("Statutory Govt", "13. P2 Royalty Deduction Register", "MANDATORY AUDIT: Detailed log of ₹2.50/Kg royalty collected and deposited"),
    ("Statutory Govt", "14. Species % Distribution", "Ecological and commercial yield breakdown (Catla, Rohu, Minor, Sawal)")
]
for idx, (cat, rn, kd) in enumerate(rep_list):
    r = rep_tbl.add_row()
    r.cells[0].paragraphs[0].text = cat
    r.cells[1].paragraphs[0].text = rn
    r.cells[2].paragraphs[0].text = kd
    style_table_row(r, rep_w, idx % 2 == 1)

doc.add_page_break()

# =========================================================================
# 9. MODULE 7: SECURITY & PERIOD-END AUDIT LOCK
# =========================================================================
h9 = doc.add_heading("9. Module 7: Security & Period-End Audit Lock", level=1)
h9.paragraph_format.space_before = Pt(12)
h9.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module Aim & Purpose (Meeting Talking Points)",
    "Aim: Ensure financial integrity, prevent fraud, and maintain tamper-proof books.\n"
    "• Sets a financial cutoff lock date (e.g. 31/03/2026).\n"
    "• Strictly blocks retroactive editing or deletion of catch tickets, inward/outward invoices, and wages sheets.\n"
    "• Enforces Role-Based Access Control (RBAC) across 5 user categories."
)

doc.add_paragraph(
    "• Role-Based Access Control (RBAC) Structure:\n"
    "  1. Super Administrator: Full read/write access across all master data, transactions, lock settings, and user management.\n"
    "  2. Chief Accountant: Full access to Wages Settlement, Cash Advances, Bank Batch Exporters, and Financial Reports.\n"
    "  3. Station Weighing Operator: Restricted to Daily Catch Intake entries for open periods at assigned stations.\n"
    "  4. Warehouse Storekeeper: Restricted to Stock Inward purchases and Outward store credit dispatches.\n"
    "  5. Government Auditor: View-only access to P1/P2 registers, species percentages, and settled payroll archives.\n\n"
    "• Period-End Audit Lock Logic (`psac_audit_lock`):\n"
    "  - When an accounting period is audited and settled, the administrator locks the date.\n"
    "  - Core controller hooks evaluate every transaction: IF (Transaction_Date <= Lock_Date) THEN ABORT.\n"
    "  - Guarantees that historical financial numbers presented to Government auditors match bank disbursement files exactly."
)

# =========================================================================
# 10. END-TO-END OPERATIONAL USER WORKFLOWS
# =========================================================================
h10 = doc.add_heading("10. End-to-End Operational Lifecycle Workflows", level=1)
h10.paragraph_format.space_before = Pt(12)
h10.paragraph_format.space_after = Pt(6)

workflows = [
    ("Workflow 1: Fisherman Enrollment",
     "1. Admin navigates to Setup -> Fishermen Directory (/setup/fishermen).\n"
     "2. Enrolls fisherman with Name, Unique Badge Code, Samiti Society, Bank Account, and IFSC.\n"
     "3. Sets opening store debt (GLD) and advance debt (ALD) if applicable.\n"
     "4. System creates active record in `psac_fishermen` ready for intake weighing."),
    
    ("Workflow 2: Daily Catch Intake & Weighing",
     "1. Station operator opens Daily Toll Matrix (/dailytoll) on mobile/tablet/desktop.\n"
     "2. Selects Date, Station Ghat, and Fisherman.\n"
     "3. Inputs scale weights for Catla, Rohu, Mrigal, Kalbasu, Aor, Lanchi, Local Minor, and Sawal.\n"
     "4. System live-computes Total Major (Tmwt), Total Catch (Twt), Gross Pay, and ₹2.50/Kg Govt Royalty.\n"
     "5. Net catch earnings are accumulated for payroll settlement."),
    
    ("Workflow 3: Store Credit Equipment Issuance",
     "1. Storekeeper opens Stock Outward (/stocks/outward).\n"
     "2. Issues nylon nets, twine, floats, or diesel to fisherman.\n"
     "3. Generates Outward Challan.\n"
     "4. Grand Total is automatically added to the fisherman's Product Liability (GLD)."),
    
    ("Workflow 4: Wages Payroll & Bank Batch Disbursement",
     "1. Chief Accountant opens Wages Settlement Matrix (/wages/sheet).\n"
     "2. Selects Billing Period (e.g. 01/08/2026 to 07/08/2026) and Samiti.\n"
     "3. System loads net catch earnings, current GLD debt, and current ALD debt.\n"
     "4. Applies dynamic recovery deductions (e.g. 25%, 50%, 100% preset).\n"
     "5. Computes Net Final Wages = Net Catch Earnings - (GLD Ded + ALD Ded).\n"
     "6. Accountant exports 13-Column Axis Bank or 12-Column ICICI Bank batch file.\n"
     "7. Batch file is uploaded to Corporate Netbanking for direct NEFT disbursement to crew bank accounts."),
    
    ("Workflow 5: Statutory Government Audit & Lockout",
     "1. Accountant generates P1 Government Intake Register and P2 Royalty Remittance Register (/reports/general).\n"
     "2. Verifies royalty fees collected against treasury remittance challans.\n"
     "3. Sets Period Audit Lock (/admin/lock) to close the billing cycle permanently.")
]

for title, steps in workflows:
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(6)
    p.paragraph_format.space_after = Pt(2)
    r_t = p.add_run(f"🔄 {title}\n")
    r_t.bold = True
    r_t.font.name = 'Calibri'
    r_t.font.color.rgb = RGBColor(31, 78, 121)
    r_s = p.add_run(steps)
    r_s.font.name = 'Calibri'
    r_s.font.size = Pt(9.5)

# Save the Word Document in multiple locations
target_word1 = r'D:\bansagar\Bansagar_Fisheries_Complete_System_Documentation.docx'
target_word2 = r'C:\Users\akdln\.gemini\antigravity\brain\fafabb32-550e-4cba-9d4d-8f4e28e8b306\Bansagar_Fisheries_Complete_System_Documentation.docx'

doc.save(target_word1)
doc.save(target_word2)

print("SUCCESS: Word Document generated at:")
print("1. " + target_word1)
print("2. " + target_word2)
