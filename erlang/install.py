import sys,os,re,shutil
d = os.path.realpath(os.path.dirname(sys.argv[0]))
path = os.path.join(d,"esocket")
fp = open(path,"rb")
lines = fp.readlines()
fp.close()
is_write = False
for i in range(len(lines)):
    line = lines[i]
    ls= re.findall("^EXEC_PATH",line)
    if ls:
        lines[i] = "EXEC_PATH=%s\n"%(d)
        is_write = True
if is_write:
    fp = open(path,"wb")
    fp.writelines(lines)
    fp.close()
etcpath = "/etc/init.d/"
if os.path.isdir(etcpath):
    print path,d
    shutil.copy(path,etcpath)
    os.chmod(os.path.join(etcpath,"esocket"),777)



