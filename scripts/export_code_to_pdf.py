#!/usr/bin/env python3
import os
from pathlib import Path
from reportlab.lib.pagesizes import A4
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Preformatted, PageBreak
from reportlab.lib.styles import getSampleStyleSheet
ROOT = Path(__file__).resolve().parent.parent
OUT_DIR = ROOT / 'exports'
OUT_DIR.mkdir(exist_ok=True)
OUT_PDF = OUT_DIR / 'code_export.pdf'
EXCLUDE_DIRS = {'.git', '__pycache__', 'node_modules', 'exports'}
TEXT_EXTS = {'.php', '.html', '.css', '.js', '.py', '.sql', '.md', '.txt', '.json', '.xml', '.ini', '.sh', '.bat'}
def is_text_file(path: Path) -> bool:
    if path.suffix.lower() in TEXT_EXTS:
        return True
    try:
        with path.open('r', encoding='utf-8') as f:
            f.read(1024)
        return True
    except Exception:
        return False
def collect_files(root: Path):
    files = []
    for dirpath, dirnames, filenames in os.walk(root):
        parts = Path(dirpath).parts
        if any(p in EXCLUDE_DIRS for p in parts):
            continue
        for fn in filenames:
            p = Path(dirpath) / fn
            if p.is_file() and is_text_file(p):
                files.append(p)
    files.sort()
    return files
def build_pdf(files, out_path: Path):
    doc = SimpleDocTemplate(str(out_path), pagesize=A4, leftMargin=36, rightMargin=36, topMargin=36, bottomMargin=36)
    styles = getSampleStyleSheet()
    story = []
    for p in files:
        story.append(Paragraph(str(p.relative_to(ROOT)), styles['Heading3']))
        story.append(Spacer(1, 6))
        try:
            text = p.read_text(encoding='utf-8')
        except Exception:
            try:
                text = p.read_text(encoding='latin-1')
            except Exception:
                text = '<binary or unreadable file skipped>'
        text = text.replace('\r\n', '\n').replace('\r', '\n')
        story.append(Preformatted(text, styles['Code']))
        story.append(PageBreak())
    if not story:
        story.append(Paragraph('No text files found to export.', styles['Normal']))
    doc.build(story)
def main():
    files = collect_files(ROOT)
    print(f'Found {len(files)} text files; generating PDF at: {OUT_PDF}')
    build_pdf(files, OUT_PDF)
    print('Done.')
if __name__ == '__main__':
    main()