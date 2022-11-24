import camelot
import sys
# import shlex

name = sys.argv[1]
dosya = sys.argv[2]
dosyaBol = dosya.split("/")
isimBol = dosyaBol[3].split(".")
dosyaOku = "/home/admin/public_html/takipsan/public/"+dosya
tables = camelot.read_pdf(dosyaOku, flavor='stream', pages='all', split_text=False, edge_tol=500, ignore_index=True )

dosyaYol = "/home/admin/public_html/takipsan/public/"+dosyaBol[0]+'/'+dosyaBol[1]+'/'+dosyaBol[2]
dbYol = dosyaBol[0]+'/'+dosyaBol[1]+'/'+dosyaBol[2]
tables.export( dosyaYol+'/'+isimBol[0]+'.xlsx', f='excel', compress=False)
print (dbYol+'/'+isimBol[0]+'.xlsx')