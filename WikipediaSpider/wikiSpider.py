import httplib2, urlparse
import sys, time, random
import multiprocessing as mp
from bs4 import BeautifulSoup


def getAnchorLinks(page):
    links = []
    for link in page.find_all('a'):
        url = link.get('href')
        if url:
            if 'wikimedia' not in url and 'mediawiki' not in url and '//' not in url:
                links.append(url)
    return links

def getPath(url):
    pURL = urlparse.urlparse(url)
    return pURL.path

def viewBot(page_source):
    visited = {}
    inQueue = []
    count = 0
    domain = "https://en.wikipedia.org"
    conn = httplib2.Http("")
    while count<=1000:
        time.sleep(random.uniform(5,10))
        page = BeautifulSoup(page_source,"html.parser")
        anchors = getAnchorLinks(page)
        if anchors:
            for anchor in anchors:
                path = getPath(anchor)
                if path not in visited:
                    inQueue.append(anchor)
                    visited[path] = 1
        if inQueue:
            anchor = inQueue.pop()
#visit the url
            anchor = domain + anchor
            (resp_header,content) = conn.request(anchor,"GET")
            writeTofile(content)
            count +=1
        else:
            print "Error!! Check with Anil Pediredla"
            break
        print ""+anchor+"visited"+"("+str(count)+"/"+str(len(inQueue))+")Visited/Queue"

def writeTofile(content):
        page = BeautifulSoup(content)
        file = open(page.title.string+".html","w")
        file.write(content)
        file.close()

def main():
        conn = httplib2.Http("")
        (resp_headers, content) = conn.request("https://en.wikipedia.org/wiki/Wikipedia", "GET")
        viewBot(content)

if __name__ == '__main__':
    main()
