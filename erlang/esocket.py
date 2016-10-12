import sys,socket,os,re
import signal
cmd = ""
if len(sys.argv)>1:
    cmd = sys.argv[1]
d = os.path.realpath(os.path.dirname(sys.argv[0]))
config_values = {}
confpath = os.path.join(d,"esocket.conf")
if os.path.isfile(confpath):
    fp = open(confpath,"rb")
    lines = fp.readlines()
    fp.close()
    for line in lines:
        ls= re.findall("^(.*?)\s*=\s*(.*)?\s*",line)
        if ls:
            key,value = ls[0]
            config_values[key]=value

path = os.path.join(d,"lib/esocket-1.0/priv/sys.config")
if os.path.isfile(path):
    fp = open(path,"rb")
    lines = fp.readlines()
    fp.close()
    is_write = False
    for i in range(len(lines)):
        line = lines[i]
        ls= re.findall("{\s*(.*?)\s*,\s*(.*?)\s*}",line)
        if ls:
            key,value = ls[0]
            if config_values.get(key) and config_values[key] != value:
                lines[i] = '{%s,%s}.\n'%(key,config_values[key])
                is_write = True
    if is_write:
        fp = open(path,"wb")
        fp.writelines(lines)
        fp.close()
epmd_port = ""
if config_values.get("epmd_port"):
    epmd_port = config_values["epmd_port"]
master_name = "master"
if config_values.get("master_name"):
    master_name = config_values["master_name"]
if cmd == "start":
    localIP = socket.gethostbyname(socket.gethostname())
    if epmd_port:
        r = []
        r.append(os.path.join(d,"bin/epmd"))
        r.append("-d -port %s -daemon"%(epmd_port))
        c = " ".join(r)
        req = os.popen(c)
        print req.read()
    r = []
    r.append(os.path.join(d,"bin/erl"))
    r.append("+K true")
    r.append("+A 100")
    r.append("+Q 1000000")
    r.append("-name %s@%s"%(master_name,localIP))
    if epmd_port:
        r.append("-epmd_port %s"%(epmd_port))
    r.append("-detached")
    c = " ".join(r)
    req = os.popen(c)
    print req.read()
elif cmd == "stop":
    c = os.path.join(d,"erts-8.0/bin/beam.smp")
    c1 = os.path.join(d,"bin/epmd")
    req = os.popen("ps ax|grep %s"%c)
    lines = req.readlines()
    for line in lines:
        ls= line.split(None,1)
        if ls and "grep " not in line:
            os.kill(int(ls[0]),signal.SIGKILL)
    req = os.popen("ps ax|grep %s"%c1)
    lines = req.readlines()
    for line in lines:
        ls= line.split(None,1)
        if ls and "grep " not in line:
            os.kill(int(ls[0]),signal.SIGKILL)


	
