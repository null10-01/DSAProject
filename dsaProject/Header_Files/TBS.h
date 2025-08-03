#ifndef TBS_H
#define TBS_H

#include <string>

using namespace std;

string convertToLower(string);

struct Node
{
    string key, val;
    Node *right;
    Node *left;

    public:
        Node(string key, string val) : key(key), val(val), right (nullptr), left(nullptr) {}
};


class Passenger
{
    public:
        string name;
        string ticketID;
        int seatNumber;

        Passenger(string n = "", string t = "", int s = 0) : name(n), ticketID(t), seatNumber(s) {}
};

class Flight
{
        Passenger bookings[100]; 
        string flightID;
        int maxSeats;
        int numberBooked;
        string date;
        string time;
        string from, to;

        public:

        Flight(string d, string t, string from, string des, string fID, int maxS = 100, int numB = 0) : date(d), time(t), from(from), to(des), flightID(fID), maxSeats(maxS), numberBooked(numB) {}

        bool createBooking(string);

        void viewBookings();
};

class newMap
{
    Node *root;

    Node *insert(Node* currNode, string key, string val);

    Node *search(Node* currNode, string key);

    public:
    newMap() : root(nullptr) {}
    
    void put(string key, string val);

    string get(string key);

};


#endif
