#!/usr/bin/env python3
"""Genera Markdown y HTML autónomo de la guía. No necesita acceso al hosting."""
import argparse,base64,html,json,re,shutil
from pathlib import Path
from html.parser import HTMLParser
ROOT=Path(__file__).resolve().parent
class Markdown(HTMLParser):
 def __init__(self):super().__init__();self.out=[];self.pre=False;self.cols=0;self.head=False;self.link=[];self.lists=[]
 def handle_starttag(self,tag,attrs):
  a=dict(attrs)
  if tag in ['p','div','figure','aside','ul','ol','table']:self.out.append('\n\n')
  if tag in ['ul','ol']:self.lists.append([tag,0])
  if tag=='li':
   if self.lists:self.lists[-1][1]+=1
   self.out.append('\n'+(str(self.lists[-1][1])+'. ' if self.lists and self.lists[-1][0]=='ol' else '- '))
  if tag in ['strong','b']:self.out.append('**')
  if tag in ['em','i']:self.out.append('*')
  if tag=='pre':self.pre=True;self.out.append('\n```text\n')
  if tag=='code' and not self.pre:self.out.append('`')
  if tag=='a':self.out.append('[');self.link.append(a.get('href',''))
  if tag=='img':self.out.append('!['+a.get('alt','')+']('+a.get('src','')+')\n')
  if tag=='thead':self.head=True
  if tag=='tr':self.cols=0;self.out.append('\n| ')
  if tag in ['td','th']:self.cols+=1
  if tag=='br':self.out.append('\n')
 def handle_endtag(self,tag):
  if tag in ['strong','b']:self.out.append('**')
  if tag in ['em','i']:self.out.append('*')
  if tag=='code' and not self.pre:self.out.append('`')
  if tag=='pre':self.pre=False;self.out.append('\n```\n')
  if tag=='a':self.out.append(']('+self.link.pop()+')')
  if tag in ['td','th']:self.out.append(' | ')
  if tag=='thead':self.out.append('\n| '+' | '.join(['---']*self.cols)+' |\n');self.head=False
  if tag in ['ul','ol'] and self.lists:self.lists.pop();self.out.append('\n')
  if tag in ['p','div','figure','aside','table']:self.out.append('\n\n')
 def handle_data(self,data):self.out.append(data)
 def text(self):
  value='\n'.join(line.rstrip() for line in ''.join(self.out).splitlines())
  value=re.sub(r'(?m)(^\|[^\n]*\|)\n+(?=\|)',r'\1\n',value)
  return re.sub(r'\n{3,}','\n\n',value).strip()
CSS=r'''
:root{--ink:#163047;--teal:#007f82;--muted:#536978;--line:#dbe4ec;--paper:#fff;--bg:#edf2f6}
*{box-sizing:border-box}body{margin:0;background:var(--bg);font-family:Arial,"DejaVu Sans",sans-serif;color:var(--ink)}
.toolbar{position:fixed;top:0;left:0;right:0;height:54px;background:#173149;color:white;z-index:5;display:flex;align-items:center;justify-content:space-between;padding:0 22px;font-size:14px}.toolbar button{background:white;color:#163047;border:0;border-radius:5px;padding:8px 13px;cursor:pointer}.toolbar small{margin-left:15px;color:#d1e4ed}
nav{position:fixed;left:0;top:54px;bottom:0;width:245px;overflow-y:auto;padding:22px 12px;background:white;border-right:1px solid var(--line);font-size:12px}nav h2{font-size:15px;margin:0 8px 15px}nav a{display:block;padding:7px 8px;text-decoration:none;color:var(--ink);line-height:1.35;border-radius:4px}nav a:hover{background:#eaf5f4}main{margin-left:245px;padding:72px 14px 25px}
.sheet{width:210mm;height:297mm;margin:0 auto 22px;background:white;padding:14mm 16mm 17mm;box-shadow:0 3px 18px #1d3c5720;position:relative;overflow:visible;break-after:page;page-break-after:always}
.sheet:last-child{break-after:auto;page-break-after:auto}.page-head{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:11px;margin-bottom:13px}.brand{font-size:18px;font-weight:bold;letter-spacing:2px}.brand span{font-size:11px;letter-spacing:.4px;font-weight:normal;margin-left:12px}.phase{font-size:11px;background:#e7f5f3;color:var(--teal);padding:6px 9px;border-radius:4px}.cut .phase{background:#fff1da;color:#915100}
h1{font-size:26px;line-height:1.17;letter-spacing:-.5px;margin:5px 0 14px}h1 .step{font-size:15px;letter-spacing:0;color:var(--teal);display:block;margin-bottom:6px}p,li{font-size:14px;line-height:1.44}p{margin:8px 0}ul,ol{padding-left:22px;margin:9px 0}li{margin:5px 0}strong,b{font-weight:700}a{color:#006f87}code{font-family:"DejaVu Sans Mono",Consolas,monospace;font-size:.9em;overflow-wrap:anywhere}
pre{font:12px/1.47 "DejaVu Sans Mono",Consolas,monospace;background:#f1f5f8;color:#163047;padding:10px 12px;margin:5px 0 10px;border:1px solid #dce5ec;border-radius:5px;white-space:pre-wrap;overflow-wrap:anywhere;position:relative}pre code{font:inherit}.code-label{font-size:10.5px;font-weight:bold;letter-spacing:.2px;color:#526d80;margin-top:8px}.copy{position:absolute;right:5px;top:4px;border:1px solid #d5e0e5;background:#fff;color:#426071;border-radius:4px;cursor:pointer;font:10px Arial;padding:3px 5px}.sheet pre:hover{outline:1px solid #d8e9ec}
table{width:100%;border-collapse:collapse;margin:10px 0;font-size:12.4px;line-height:1.4}td,th{border:1px solid var(--line);padding:6px 8px;text-align:left;vertical-align:top;overflow-wrap:anywhere}th{background:#edf4f8;font-weight:bold}tr:nth-child(even) td{background:#fafcfd}
figure{margin:10px 0}figure img{width:100%;height:auto;max-height:var(--max-height,80mm);object-fit:contain;display:block;border:1px solid #d9e2ea;border-radius:5px;cursor:zoom-in;background:#fff}figcaption{font-size:10.2px;line-height:1.36;color:var(--muted);margin-top:5px}
aside{background:#fff6e6;border-left:3px solid #df9b31;padding:10px 12px;margin:11px 0;font-size:12.6px;line-height:1.42}aside code{font-size:.9em}.result{margin-top:11px;border-left:3px solid var(--teal);padding:8px 11px;background:#edf8f5;font-size:12.7px;line-height:1.4}.result b{color:#006e6e}.page-foot{position:absolute;left:16mm;right:16mm;bottom:8mm;border-top:1px solid var(--line);padding-top:7px;display:flex;justify-content:space-between;gap:10px;font-size:10px;color:var(--muted)}.check{font-size:10px;display:flex;align-items:center;gap:4px}.check input{margin:0}.cover-route{margin:28px 0;display:grid;grid-template-columns:160px 1fr;gap:18px 15px;padding:20px;background:#173149;color:white;border-radius:8px}.cover-route b{font-size:20px}.cover-route span{font-size:15px;line-height:1.45}.cover h1{font-size:47px;margin-top:38px;margin-bottom:18px}.cover p{font-size:16px;line-height:1.5}.cover aside{margin-top:24px}.cover .page-head{border:0}.sheet.tight p,.sheet.tight li{font-size:13.2px;line-height:1.39}.sheet.tight table{font-size:11.8px}.sheet.tight td,.sheet.tight th{padding:5px 7px}.sheet.tight figure{margin:8px 0}.sheet.tight aside{font-size:12px;padding:8px 10px}.sheet.tight pre{font-size:11.6px}
#zoom{display:none;position:fixed;inset:0;background:#112234e8;z-index:20;align-items:center;justify-content:center;padding:35px;cursor:zoom-out}#zoom.open{display:flex}#zoom img{max-width:96vw;max-height:92vh;object-fit:contain;background:white}#zoom button{position:absolute;right:18px;top:12px;background:white;border:0;border-radius:5px;padding:8px 12px}
@media(max-width:1100px){nav{display:none}main{margin-left:0}.toolbar small{display:none}}
@media(max-width:820px){main{padding-left:0;padding-right:0}.sheet{width:100%;height:auto;min-height:297mm;margin-bottom:18px;padding:25px 22px 60px}.page-foot{left:22px;right:22px}.cover h1{font-size:38px}.toolbar{font-size:12px}}
@page{size:A4;margin:0}
@media print{body{background:white}.toolbar,nav,.copy,.check,#zoom{display:none!important}main{margin:0;padding:0}.sheet{width:210mm;height:297mm;min-height:0;margin:0;padding:14mm 16mm 17mm;box-shadow:none;overflow:visible}a{text-decoration:none}figure img{cursor:default}.sheet pre:hover{outline:none}}
'''
JS=r'''
(function(){
 const key='egs-migracion-cpanel-20261016';let checked={};try{checked=JSON.parse(localStorage.getItem(key)||'{}')}catch(e){}
 document.querySelectorAll('.check input').forEach(e=>{e.checked=!!checked[e.dataset.page];e.addEventListener('change',()=>{checked[e.dataset.page]=e.checked;try{localStorage.setItem(key,JSON.stringify(checked))}catch(x){}update()})});
 function update(){let count=document.querySelectorAll('.check input:checked').length;document.getElementById('progress').textContent=count+' pasos marcados'}update();
 document.querySelectorAll('pre').forEach(p=>{const b=document.createElement('button');b.className='copy';b.textContent='Copiar';b.addEventListener('click',async()=>{const s=p.querySelector('code').textContent;try{await navigator.clipboard.writeText(s);b.textContent='Copiado'}catch(e){const a=document.createElement('textarea');a.value=s;document.body.append(a);a.select();document.execCommand('copy');a.remove();b.textContent='Copiado'}setTimeout(()=>b.textContent='Copiar',1500)});p.append(b)});
 const zoom=document.getElementById('zoom');document.querySelectorAll('figure img').forEach(i=>i.addEventListener('click',()=>{zoom.querySelector('img').src=i.src;zoom.querySelector('img').alt=i.alt;zoom.classList.add('open')}));zoom.addEventListener('click',()=>zoom.classList.remove('open'));document.addEventListener('keydown',e=>{if(e.key==='Escape')zoom.classList.remove('open')});
})();
'''
def main():
 a=argparse.ArgumentParser();a.add_argument('--output',type=Path,required=True);args=a.parse_args();out=args.output.resolve();out.mkdir(parents=True,exist_ok=True)
 pages=json.loads((ROOT/'contenido.json').read_text());sections=[];toc=[];md=['# Guía visual: migración de EGS con cPanel\n'];commands=['# Comandos de apoyo · EGS\n\nCopia sólo el bloque del paso correspondiente. No ejecutes este documento como script ni pegues todos los bloques juntos. Linux y PowerShell de Windows son entornos distintos.\n']
 for i,page in enumerate(pages,1):
  title=page['title'];number=page.get('number');body=page['body'];is_cut=page['phase']=='Corte coordinado';classes='sheet'+(' cover' if i==1 else '')+(' cut' if is_cut else '')
  # Las imágenes se incrustan para que el HTML se pueda abrir sin red.
  def embed(m):
   path=ROOT/m.group(1);data=base64.b64encode(path.read_bytes()).decode();return 'src="data:image/png;base64,'+data+'"'
  body_inline=re.sub(r'src="(capturas/[^\"]+)"',embed,body)
  result=('<div class="result"><b>Resultado esperado · </b>'+page['result']+'</div>') if page.get('result') else ''
  label=('<span class="step">Paso '+html.escape(number)+'</span>') if number is not None else ''
  check=('<label class="check"><input type="checkbox" data-page="'+str(i)+'">Paso completado</label>') if number is not None else '<span>Referencia visual · cPanel</span>'
  sections.append('<section class="'+classes+'" id="pagina-'+str(i)+'"><header class="page-head"><div class="brand">EGS<span>Guía de migración</span></div><div class="phase">'+page['phase']+'</div></header><h1>'+label+html.escape(title)+'</h1><article>'+body_inline+result+'</article><footer class="page-foot">'+check+'<span>16 oct 2026 · '+str(i)+' / '+str(len(pages))+'</span></footer></section>')
  toc.append('<a href="#pagina-'+str(i)+'">'+(number+' · ' if number is not None else '')+html.escape(title)+'</a>')
  parser=Markdown();parser.feed(body);md+=['\n## '+(number+' · ' if number is not None else '')+title+'\n',parser.text()+'\n']
  if page.get('result'):md+=['\n**Resultado esperado:** '+page['result']+'\n']
  blocks=re.findall(r'<pre><code>(.*?)</code></pre>',body,re.S)
  if blocks:
   commands+=['\n## '+(number+' · ' if number is not None else '')+title+'\n']
   for block in blocks:commands+=['\n```text\n'+html.unescape(block)+'\n```\n']
 doc='<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>EGS · Guía visual de migración con cPanel</title><style>'+CSS+'</style></head><body><div class="toolbar"><div>EGS · Migración al nuevo hosting<small id="progress"></small></div><button onclick="window.print()">Imprimir / PDF</button></div><nav aria-label="Índice"><h2>Guía paso a paso</h2>'+''.join(toc)+'</nav><main>'+''.join(sections)+'</main><div id="zoom" role="dialog" aria-label="Captura ampliada"><button>Cerrar · Esc</button><img alt=""></div><script>'+JS+'</script></body></html>'
 (out/'GUIA_VISUAL_MIGRACION_EGS.html').write_text(doc);(ROOT/'GUIA_PASO_A_PASO.md').write_text('\n'.join(md));(out/'COMANDOS.md').write_text('\n'.join(commands)+'\n\n'+(ROOT/'APENDICE_WINDOWS.md').read_text())
 (out/'capturas').mkdir(exist_ok=True)
 for path in (ROOT/'capturas').glob('*.png'):shutil.copyfile(path,out/'capturas'/path.name)
 shutil.copyfile(ROOT/'GUIA_PASO_A_PASO.md',out/'GUIA_PASO_A_PASO.md')
 template=ROOT.parents[2]/'ops/migration/hosting.example.json';shutil.copyfile(template,out/'destino.ejemplo.json')
 readme='''# Guía visual de migración EGS

1. Abre GUIA_VISUAL_MIGRACION_EGS.pdf para leer o imprimir.
2. Abre GUIA_VISUAL_MIGRACION_EGS.html en tu navegador para usar el índice, marcar pasos y copiar comandos. Las capturas están incrustadas y funciona sin conexión.
3. COMANDOS.md reúne los bloques por paso. No es un script para ejecutarlo de una vez.
4. destino.ejemplo.json es una plantilla sin credenciales reales. Complétala en la carpeta privada del servidor nuevo.

Referencia asumida: cPanel Jupiter. Las imágenes proceden de la demostración pública, documentación oficial o visores de archivos del kit; no son capturas de tu nueva cuenta.

La guía no contiene el paquete de respaldo ni su llave. Se transfieren por separado mediante el procedimiento privado ya preparado. Mantén la copia preliminar como ensayo y genera una final durante el corte coordinado.
'''
 (out/'LEEME.md').write_text(readme)
 print(json.dumps({'pages':len(pages),'output':str(out),'embedded_screenshots':len(re.findall('data:image/png',doc))},ensure_ascii=False))
if __name__=='__main__':main()
