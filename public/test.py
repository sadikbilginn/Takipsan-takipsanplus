import camelot
import sys

dosya = 'upload/files/ms/000740369007a2e18705e7b35f8d875b.pdf'
dosyaBol = dosya.split("/")
isimBol = dosyaBol[3].split(".")
dosyaOku = "/home/admin/public_html/takipsan/public/"+dosya
tables = camelot.read_pdf(dosyaOku, flavor='stream', pages='all', split_text=False, edge_tol=500, ignore_index=True )

dosyaYol = "/home/admin/public_html/takipsan/public/"+dosyaBol[0]+'/'+dosyaBol[1]+'/'+dosyaBol[2]
dbYol = dosyaBol[0]+'/'+dosyaBol[1]+'/'+dosyaBol[2]
tables.export( dosyaYol+'/'+isimBol[0]+'.xlsx', f='excel', compress=False)
print (dbYol+'/'+isimBol[0]+'.xlsx')