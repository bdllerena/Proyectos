#! /usr/bin/env python

import sys
import os
import time
import argparse
import getopt
import locale
from dialog import Dialog
import ConfigParser
import requests
import re
import email
from  dateutil.parser import *

locale.setlocale(locale.LC_ALL, '')

def usage():
	"Output help screen for using this program"

	print "Usage: " + sys.argv[0]
	print
	print "The following shell variables can be set in ~/.seeddms-upload.conf"
	print "  baseurl: url of restapi service"
	print "  username: name of user to be used"
	print "  password: password of user to be used. If not set, it will be asked."
	print "  targetfolder: id of folder where file is uploaded"

message = sys.stdin.read()

def upload_chunck(foo, gauge):
    prevnl = -1
    while True:
        nextnl = prevnl + (1 << 13)
        if nextnl > len(foo):
            d.gauge_update(100, 'Successfully uploaded!', True)
            yield foo[prevnl + 1:len(foo)]
            break;
        else:
            d.gauge_update(nextnl*100/len(foo))
            yield foo[prevnl + 1:nextnl]
        prevnl = nextnl-1

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("-c", "--config", help="read this config file")
    args= parser.parse_args()
    d = Dialog(dialog="dialog")
    d.set_background_title("Upload Mail into SeedDMS")
    if args.config:
        config = ConfigParser.RawConfigParser()
        if config.read(args.config):
            if len(config.sections()) == 1:
                section = config.sections()[0]
                baseurl = config.get(section, 'baseurl')
                username = config.get(section, 'username')
                password = config.get(section, 'password')
                targetfolder = config.get(section, 'targetfolder')
            else:
                server = []
                for x in config.sections():
                    server.append((x, x))
                code, section = d.menu("Select server", choices=server)
                if code == d.OK:
                    baseurl = config.get(section, 'baseurl')
                    username = config.get(section, 'username')
                    password = config.get(section, 'password')
                    targetfolder = config.get(section, 'targetfolder')
                else:
                    d.msgbox("No server selected")
            if baseurl == '':
                d.msgbox("No base URL set")
                sys.exit(1)
            if targetfolder == '':
                d.msgbox("No target folder set")
                sys.exit(1)
            if password == '':
                code, password = d.passwordbox("Password")

            r = requests.post(baseurl + '/login', {'user': username, 'pass': password})
            j = r.json()
            if j['success'] == False:
                d.msgbox("Could not login")
                sys.exit(1)
            cookies = r.cookies

            folderids = targetfolder.split(',')
            if len(folderids) > 1:
                folders = []
                for x in folderids:
                    r = requests.get(baseurl + '/folder/' + x, cookies=cookies)
                    j = r.json()
                    if j['success'] == True:
                        folders.append((x, j['data']['name']))
                if len(folders) > 1:
                    print folders
                    code, folderid = d.menu("Select folder", choices=folders)
                    if code == d.OK:
                        targetfolderid = folderid
                else:
                    targetfolderid = 0
            else:
                targetfolderid = targetfolder
        else:
            d.msgbox("Error reading config file")
            sys.exit(1)
    else:
        d.msgbox("No config file given")
        sys.exit(1)

    r = requests.get(baseurl + '/folder/' + targetfolderid, cookies=cookies)
    j = r.json()
    if j['success'] == False:
        d.msgbox("Could not get target folder")
        sys.exit(1)

    msg = email.message_from_string(message)
    dobj = parse(msg['date'])

    d.gauge_start("Uploading email ...")
    r = requests.put(baseurl + '/folder/' + targetfolderid + '/document', data=upload_chunck(message,d), params={'name':msg['subject']+'-'+dobj.strftime('%Y-%m-%d'), 'origfilename': msg['subject']+'-'+dobj.strftime('%Y-%m-%d')+'.eml'}, cookies=cookies)
    d.gauge_stop()
    j = r.json()
    if j['success'] == False:
        d.msgbox("Could not upload email")
        sys.exit(1)

    sys.exit(0)
