from bs4 import BeautifulSoup
import urllib2
import re
from HTMLParser import HTMLParser

class MLStripper(HTMLParser):
    def __init__(self):
        self.reset()
        self.fed = []
    def handle_data(self, d):
        self.fed.append(d)
    def get_data(self):
        return ''.join(self.fed)

def strip_tags(html):
    s = MLStripper()
    s.feed(html)
    return s.get_data()

def _call(str):
	return re.sub('<[A-Za-z\/][^>]*>', '', str)


wiki = "http://en.wikipedia.org/wiki/Rajinikanth"
#header = {'User-Agent': 'Mozilla/5.0'} #Needed to prevent 403 error on Wikipedia
#req = urllib2.Request(wiki,headers=header)
page = urllib2.urlopen(wiki)
soup = BeautifulSoup(page)


#get name
table = soup.find("table", { "class" : "infobox biography vcard" })

print table

name = soup.find("span", {"class" : "fn"})
just_name = name.contents[0]
print just_name


#get first paragraph
for row in soup.findAll("p"):
    print _call(''.join([str(i) for i in row]))
    print strip_tags(''.join([str(i) for i in row]))
    break
	




 
