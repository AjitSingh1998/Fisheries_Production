import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls
import os

doc = docx.Document()

# Margins (1 inch)
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

# =========================================================================
# COVER / TITLE SECTION
# =========================================================================
title_p = doc.add_paragraph()
title_p.paragraph_format.space_before = Pt(20)
title_p.paragraph_format.space_after = Pt(4)
r_main_title = title_p.add_run("FISHERY SALES & PRODUCTION-TO-SELL APP")
r_main_title.font.name = 'Calibri'
r_main_title.font.size = Pt(24)
r_main_title.font.bold = True
r_main_title.font.color.rgb = RGBColor(31, 78, 121)

sub_p = doc.add_paragraph()
sub_p.paragraph_format.space_after = Pt(16)
r_sub = sub_p.add_run("High-Level Software Requirements Specification (SRS) & Module Architecture")
r_sub.font.name = 'Calibri'
r_sub.font.size = Pt(13)
r_sub.font.italic = True
r_sub.font.color.rgb = RGBColor(89, 89, 89)

# Metadata Box
meta_tbl = doc.add_table(rows=5, cols=2)
meta_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
meta_data = [
    ("Document Type:", "High-Level Software Requirements Specification (SRS)"),
    ("System Domain:", "Commercial Fishery Supply Chain, Quality Sorting, POS & Intercity Logistics ERP"),
    ("Core Operational Model:", "Point Inward -> Quality Sorting -> Smart Toll & 1-Click POS -> Ice Cold Storage -> Intercity Dispatch"),
    ("Key Capabilities:", "3-Way Quality Grading, Bachat Leftover Toll, 1-Click Scale POS/Box Printing, Fleet Logistics"),
    ("Target Users:", "Depot Managers, Weighing Operators, Floor Supervisors, Wholesale Sales Agents, Dispatch In-charge")
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
# 1. INTRODUCTION & END-TO-END WORKFLOW
# =========================================================================
h1 = doc.add_heading("1. System Introduction & Business Workflow", level=1)
h1.paragraph_format.space_before = Pt(12)
h1.paragraph_format.space_after = Pt(6)

doc.add_paragraph(
    "The Fishery Sales & Production-to-Sell ERP is an end-to-end commercial supply chain platform designed specifically for "
    "high-volume fish processing depots. It manages the complete operational lifecycle from the moment raw fish arrives from multiple "
    "reservoir points/ghats via boats, tractors, and trucks, through quality sorting, intelligent prioritized weighing, 1-click spot "
    "sales & box barcode label printing, cold storage floor reconciliation, and intercity fleet dispatches."
)

add_callout(
    doc,
    "The 7 Core Operational Phases of the Platform",
    "1. Point Arrival & Transit: Fish crates transported from various catching points to the central depot.\n"
    "2. Quality Grading: Manual sorting into Fresh Fish, Rotten Fish (rejected), and Spoiled Fish (damaged).\n"
    "3. Prioritized Toll Matrix: Single-species crate priority (35-40 kg), 2-variant limit, and separate Bachat (mix) toll.\n"
    "4. 1-Click Multi-Action Weighing: Weighing automatically triggers live Spot Invoicing OR Box Packaging with Thermal Slips.\n"
    "5. Floor Closing & Ice Preservation: Leftover floor fish weighed, packed in crates, sealed with crushed ice for cold storage.\n"
    "6. Intercity Dispatch Management: Grouping boxes into vehicle shipments with driver trip sheets and destination tracking.\n"
    "7. Customer Financials & Yield Analytics: Reconciliation of spot cash, wholesale credit ledgers, and quality loss ratios."
)

# =========================================================================
# 2. MODULE 1: MASTER SETUP & LOGISTICS
# =========================================================================
h2 = doc.add_heading("2. Module 1: Master Data, Locations & Logistics Setup", level=1)
h2.paragraph_format.space_before = Pt(12)
h2.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 1 Aim & Purpose",
    "Aim: Establish the operational foundations for locations, species grades, packaging specs, pricing, and buyers.\n"
    "• Configures collection ghats/points, transit depots, and destination wholesale city markets.\n"
    "• Defines species catalog with 3-tier quality pricing (Fresh, Rotten, Spoiled).\n"
    "• Registers vehicles, drivers, buyers/customers, and digital weighing scale calibrations."
)

setup_items = [
    ("Location & Depot Master", "Defines catching points/ghats (e.g. Ghat A, Ghat B), central processing depots, and destination intercity markets (e.g. Delhi, Kolkata, Mumbai, Nagpur, Prayagraj)."),
    ("Species & Quality Catalog", "Maintains species variants (Rohu, Catla, Mrigal, Kalbasu, Aor, Lanchi, Minor, Sawal) mapped to 3 Quality Tiers: 1. Fresh (Prime), 2. Rotten (Scrap/0-rate), 3. Spoiled (Discounted rate)."),
    ("Packaging Specifications", "Defines crate/carret standard weights (35-40 Kg capacity), box tare weights, and crushed ice allowance factors."),
    ("Customer & Trader Directory", "Maintains wholesale buyers, local depot traders, contact details, credit limits, and billing terms (Cash, Spot UPI, Credit Ledger)."),
    ("Fleet & Vehicle Master", "Registers transport vehicles (Trucks, Pickups, Tractors), license plate numbers, vehicle carrying capacities, and assigned drivers with contact details.")
]
for title, desc in setup_items:
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(2)
    r_t = p.add_run(f"• {title}: ")
    r_t.bold = True
    r_t.font.color.rgb = RGBColor(31, 78, 121)
    p.add_run(desc)

# =========================================================================
# 3. MODULE 2: DEPOT INTAKE, QUALITY SORTING & SMART TOLL MATRIX
# =========================================================================
h3 = doc.add_heading("3. Module 2: Depot Intake, Quality Sorting & Smart Toll Matrix", level=1)
h3.paragraph_format.space_before = Pt(12)
h3.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 2 Aim & Purpose",
    "Aim: Control incoming fish arrivals, enforce rigid quality sorting, and execute prioritized digital weighing.\n"
    "• Sorts raw fish into Fresh, Rotten, and Spoiled lots before weighing.\n"
    "• Enforces strict single-species and max 2-variant weighing priority rules.\n"
    "• Reconciles small leftover quantities across points through automated Bachat (Mix) Tolling."
)

doc.add_heading("Intelligent Toll Priority Algorithm (Steps A to G):", level=2)

prio_steps = [
    ("Step A - Pure Single-Species Toll", "Operator tolls carrets containing exclusively one fish variant (e.g. 100% Rohu or 100% Catla) weighing 35-40 kg."),
    ("Step B - Avoid Cross-Contamination", "Strict validation ensures multiple species are not mixed when sufficient single-species volume exists."),
    ("Step C - Maximum 2-Variant Rule", "If a pure crate cannot be filled, allow a maximum of 2 variants (e.g. Rohu + Catla mix = 35-40 kg)."),
    ("Step D - Restrict 3+ Variant Mixing", "Rejects crate tolling with 3 or more species to preserve commercial batch grading."),
    ("Step E - Leftover Staging", "Odd fish or remaining small quantities below crate threshold (<35 kg) are set aside in the staging bay."),
    ("Step F - Sequential Point Completion", "Repeat steps A through E sequentially for all collection points/ghats."),
    ("Step G - Bachat Toll (Mix Point Toll)", "Once all primary point tolls are completed, all staged leftover fish from all points are pooled and weighed as 'Bachat Toll' (Reconciled Mix Intake).")
]
for step, text in prio_steps:
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(2)
    r_s = p.add_run(f"🔹 {step}: ")
    r_s.bold = True
    r_s.font.color.rgb = RGBColor(31, 78, 121)
    p.add_run(text)

doc.add_page_break()

# =========================================================================
# 4. MODULE 3: 1-CLICK MULTI-ACTION TOLL, SPOT POS & BOX PACKAGING
# =========================================================================
h4 = doc.add_heading("4. Module 3: 1-Click Multi-Action Toll, Spot POS & Box Packaging", level=1)
h4.paragraph_format.space_before = Pt(12)
h4.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 3 Aim & Purpose",
    "Aim: High-speed live floor operations combining Intake, Counter Sale, and Box Packaging into 1 single click.\n"
    "• Integrates with electronic weighing scale for instant weight capture.\n"
    "• Enables immediate Spot Sale to visiting buyers with automated invoice generation.\n"
    "• Generates unique Box Serial Numbers and prints Thermal Barcode Labels instantly."
)

doc.add_paragraph(
    "During weighing on the floor, the operator performs a 3-way unified action on a single screen:\n\n"
    "1. Live Intake Counting:\n"
    "   - Captures Point ID, Fish Variant(s), Quality Grade (Fresh/Rotten/Spoiled), Carret Weight (35-40 Kg), and Piece Count.\n\n"
    "2. Instant Spot Sale (POS Invoicing in Background):\n"
    "   - If a buyer/trader is present on the floor, operator selects the Customer from the dropdown.\n"
    "   - The system instantly generates a Sales Invoice in the background capturing the exact crate weight, rate/kg, and payable amount without interrupting the weighing queue.\n\n"
    "3. Dispatch Box Packaging & Thermal Slip Printing:\n"
    "   - A toggle checkbox allows marking the unit as a 'Dispatch Box' vs a 'Floor Carret'.\n"
    "   - When 'Box' is checked, the system instantly generates a unique Box Number (e.g. BOX-2026-0891) and sends a print command to the thermal barcode printer.\n"
    "   - Thermal Label Slip Output: Box Number, Date & Time, Origin Depot, Fish Variant(s), Quality Grade, Piece Count, Net Weight (Kg), and Tare Weight."
)

# =========================================================================
# 5. MODULE 4: ICE COLD STORAGE & CLOSING FLOOR RECONCILIATION
# =========================================================================
h5 = doc.add_heading("5. Module 4: Floor Closing, Ice Preservation & Cold Storage", level=1)
h5.paragraph_format.space_before = Pt(12)
h5.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 4 Aim & Purpose",
    "Aim: Finalize floor production, seal unsold inventory with crushed ice, and reconcile depot closing stock.\n"
    "• Weighed floor sweep fish recorded as Closing Production.\n"
    "• Manages crushed ice sealing and cold room storage ledger.\n"
    "• Calculates daily production balance: Total Intake = Spot Sales + Prepared Boxes + Cold Storage."
)

doc.add_paragraph(
    "• Floor Closing Toll Workflow:\n"
    "  1. After point tolling, spot selling, and box packaging are finished, any remaining fish on the floor is gathered.\n"
    "  2. Weighed as 'Closing Floor Production' carret-wise.\n"
    "  3. Crates are sealed with crushed ice to prevent spoilage.\n"
    "  4. Moved into the Depot Cold Storage Room with assigned Bay/Rack numbers.\n\n"
    "• Depot Floor Balance Equation:\n"
    "  Total Daily Intake = Spot Counter Sales + Packaged Dispatch Boxes + Cold Storage Stock + Quality Rejections (Rotten)"
)

doc.add_page_break()

# =========================================================================
# 6. MODULE 5: INTERCITY DISPATCH & FLEET LOGISTICS
# =========================================================================
h6 = doc.add_heading("6. Module 5: Intercity Dispatch & Fleet Logistics Management", level=1)
h6.paragraph_format.space_before = Pt(12)
h6.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 5 Aim & Purpose",
    "Aim: Manage wholesale multi-box truck shipments to distant metropolitan fish markets.\n"
    "• Creates vehicle manifests grouping hundreds of prepared boxes by destination city.\n"
    "• Captures transport logistics: Vehicle #, Driver name/phone, Transit distance (Km), Departure time.\n"
    "• Generates transit gate passes, delivery challans, and real-time destination delivery tracking."
)

disp_tbl = doc.add_table(rows=1, cols=3)
disp_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
disp_w = [1.8, 2.2, 2.5]
for idx, h in enumerate(["Field Group", "Captured Attributes", "Operational & Financial Purpose"]):
    disp_tbl.cell(0, idx).paragraphs[0].text = h
style_table_header(disp_tbl.rows[0], disp_w)

disp_fields = [
    ("Origin & Destination", "From Depot (e.g. Bansagar Central Depot), Destination City (e.g. Delhi Gazipur, Kolkata Howrah, Mumbai)", "Determines route, transit hours, ice preservation requirement, and freight rates"),
    ("Transport & Fleet", "Vehicle Number, Vehicle Type (Insulated Truck, Pickup), Driver Name, Driver Mobile, Transit Distance (Km)", "Fleet accountability, gate security pass, and driver trip advance tracking"),
    ("Shipment Manifest", "List of Box Serial Numbers, Species breakdown, Total Box Count, Gross Shipment Weight (Kg)", "Auditable chain-of-custody verifying every box loaded onto the truck"),
    ("Consignee & Buyer", "Destination Commission Agent / Wholesale Buyer, Advance Freight Paid, Balance Payable on Delivery", "Generates Dispatch Challan and debits buyer account upon departure")
]
for idx, (fg, ca, ofp) in enumerate(disp_fields):
    r = disp_tbl.add_row()
    r.cells[0].paragraphs[0].text = fg
    r.cells[1].paragraphs[0].text = ca
    r.cells[2].paragraphs[0].text = ofp
    style_table_row(r, disp_w, idx % 2 == 1)

# =========================================================================
# 7. MODULE 6: BILLING, CUSTOMER LEDGERS & ACCOUNTS RECEIVABLE
# =========================================================================
h7 = doc.add_heading("7. Module 6: Billing, Customer Accounts & Credit Ledger", level=1)
h7.paragraph_format.space_before = Pt(12)
h7.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 6 Aim & Purpose",
    "Aim: Real-time commercial revenue management, wholesale credit ledgers, and payment receipts.\n"
    "• Reconciles Spot Cash/UPI counter sales with wholesale intercity dispatch invoices.\n"
    "• Manages buyer credit limits, payment receipts, and aging account ledgers.\n"
    "• Calculates transportation deductions, ice costs, and commission agent net realizations."
)

doc.add_paragraph(
    "1. Spot Counter Invoicing: Instant cash/UPI invoices generated during live weighing for local buyers.\n"
    "2. Dispatch Consignment Invoicing: Wholesale invoices generated per truck shipment based on manifest box weights and agreed destination rates/kg.\n"
    "3. Buyer Credit Ledger: Comprehensive statement of invoices billed, payments received (NEFT/RTGS/Cash), transit damage claims, and outstanding balances."
)

doc.add_page_break()

# =========================================================================
# 8. MODULE 7: EXECUTIVE INTELLIGENCE, YIELD & LOSS REPORTING
# =========================================================================
h8 = doc.add_heading("8. Module 7: Executive Intelligence, Yield & Quality Loss Analytics", level=1)
h8.paragraph_format.space_before = Pt(12)
h8.paragraph_format.space_after = Pt(6)

add_callout(
    doc,
    "Module 7 Aim & Purpose",
    "Aim: Executive visibility into point productivity, quality grading losses, and sales realization margins.\n"
    "• Tracks quality losses (% Rotten vs Spoiled vs Fresh) per catching ghat.\n"
    "• Reconciles intake weight against dispatched sale weight (transit shrinkage & ice tare).\n"
    "• Monitors vehicle profitability and intercity market price realizations."
)

rep_items = [
    ("Point Yield & Quality Loss Audit", "Compares fish intake across all catching ghats and highlights points with abnormally high Rotten/Spoiled percentages due to transit delays."),
    ("Daily 3-Way Reconciliation Matrix", "Reconciles: Total Inward Scale Weight = Spot Sales Weight + Dispatch Box Weight + Cold Storage Closing Stock + Rotten Scrap."),
    ("City-wise Sales Realization Report", "Compares net revenue realized across destination cities (e.g. Delhi vs Kolkata vs Mumbai) factoring in freight and ice expenses."),
    ("Box Inventory & Traceability Log", "Full lifecycle history of every box: Box # -> Catching Point -> Species Variant -> Pack Time -> Dispatch Truck -> Consignee Buyer.")
]
for title, desc in rep_items:
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after = Pt(2)
    r_t = p.add_run(f"📊 {title}: ")
    r_t.bold = True
    r_t.font.color.rgb = RGBColor(31, 78, 121)
    p.add_run(desc)

# =========================================================================
# 9. SUMMARY MATRIX FOR MEETINGS
# =========================================================================
h9 = doc.add_heading("9. Executive Summary Matrix for Meetings", level=1)
h9.paragraph_format.space_before = Pt(12)
h9.paragraph_format.space_after = Pt(6)

sum_tbl = doc.add_table(rows=1, cols=4)
sum_tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
sum_w = [1.5, 1.8, 1.8, 1.4]
for idx, h in enumerate(["Module Name", "Primary User Role", "Key Inputs / Triggers", "Primary Deliverable"]):
    sum_tbl.cell(0, idx).paragraphs[0].text = h
style_table_header(sum_tbl.rows[0], sum_w)

matrix_data = [
    ("1. Master Logistics Setup", "Super Admin / Manager", "Points, Depots, Cities, Species, Quality Tiers, Vehicles, Drivers", "Central Master Registry"),
    ("2. Quality & Smart Toll", "Weighing Operator, Labor", "Fresh/Rotten/Spoiled sorting, Point selection, Scale weights", "Graded Carret Intake Tickets & Bachat Toll"),
    ("3. 1-Click POS & Packaging", "Weighing Operator, Salesman", "Scale weight, Customer select, Box/Carret checkbox toggle", "Spot Sales Invoices & Thermal Box Labels"),
    ("4. Ice Cold Storage", "Floor Supervisor", "Remaining floor fish, Crushed ice crates, Cold room bay", "Closing Stock Ledger & Preserved Crates"),
    ("5. Intercity Dispatch", "Dispatch In-charge", "Destination City, Vehicle #, Driver, Box Barcode Scan", "Vehicle Dispatch Manifest & Gate Pass"),
    ("6. Billing & Ledgers", "Chief Accountant", "Invoices, Spot Cash, Bank Transfers, Transit claims", "Buyer Credit Ledgers & Realization"),
    ("7. Yield & Loss Analytics", "Executive Board, Owners", "Intake vs Dispatched weights, Quality grade loss ratios", "Executive Yield & Margin Dashboards")
]
for idx, (m, u, i, d) in enumerate(matrix_data):
    r = sum_tbl.add_row()
    r.cells[0].paragraphs[0].text = m
    r.cells[1].paragraphs[0].text = u
    r.cells[2].paragraphs[0].text = i
    r.cells[3].paragraphs[0].text = d
    style_table_row(r, sum_w, idx % 2 == 1)

# Save files
target_srs1 = r'D:\bansagar\Fishery_Sales_App_High_Level_SRS.docx'
target_srs2 = r'C:\Users\akdln\.gemini\antigravity\brain\fafabb32-550e-4cba-9d4d-8f4e28e8b306\Fishery_Sales_App_High_Level_SRS.docx'

doc.save(target_srs1)
doc.save(target_srs2)

print("SUCCESS: High Level SRS Word Document generated at:")
print("1. " + target_srs1)
print("2. " + target_srs2)
