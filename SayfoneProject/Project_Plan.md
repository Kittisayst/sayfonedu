
 protected static ?int $navigationSort = 1;
                'ຈັດການບັນຊີຜູ້ໃຊ້', 1
                'ຈັດການຂໍ້ມູນນັກຮຽນ', 2
                'ຈັດການຂໍ້ມູນຜູ້ປົກຄອງ', 3
                'ຈັດການຊັ້ນຮຽນ ແລະ ຫ້ອງຮຽນ', 4
                'ຈັດການການຮຽນການສອນ', 5
                'ຈັດການການເງິນ', 6
                'ການສື່ສານ', 7
                'ຮ້ານຄ້າໂຮງຮຽນ', 8
                'ຫ້ອງສະໝຸດດິຈິຕອລ', 9
                'ກິດຈະກຳນອກຫຼັກສູດ', 10
                'ຕັ້ງຄ່າ ແລະ ບຳລຸງຮັກສາ', 11
                'ຈັດການຂໍ້ມູນມາດຕະຖານ', 12
1. ລະບົບຈັດການບັນຊີຜູ້ໃຊ້
# ລາຍການ Process
✅ ການລົງທະບຽນຜູ້ໃຊ້ໃໝ່
✅ ການເຂົ້າສູ່ລະບົບແລະຢືນຢັນຕົວຕົນ
✅ ການກຳນົດບົດບາດແລະສິດທິ
ການລົງທະບຽນຂໍ້ມູນຊີວະມິຕິ
✅ ການຕິດຕາມກິດຈະກຳຂອງຜູ້ໃຊ້

# Data Store ທີ່ກ່ຽວຂ້ອງ
Users (D1)
Roles (D2)
Permissions (D3)
Role_Permissions (D4)
User_Activities (D5)
Biometric_Data (D6)
Biometric_Logs (D7)

2. ລະບົບຈັດການຂໍ້ມູນນັກຮຽນ
# ລາຍການ Process
✅ ການລົງທະບຽນນັກຮຽນໃໝ່
✅ ການບັນທຶກປະຫວັດການສຶກສາກ່ອນໜ້າ
✅ ການບັນທຶກຂໍ້ມູນສຸຂະພາບແລະຄວາມຕ້ອງການພິເສດ
✅ ການບັນທຶກພຶດຕິກຳແລະຜົນງານ
✅ ການບັນທຶກເອກະສານສຳຄັນ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Students (D14)
Student_Previous_Education (D21)
Student_Health_Records (D19)
Student_Special_Needs (D24)
Student_Previous_Locations (D15)
Student_Interests (D16)
Student_Achievements (D22)
Student_Behavior_Records (D23)
Student_Documents (D18)
Student_Emergency_Contacts (D20)
Student_Siblings (D17)

3. ລະບົບຈັດການຂໍ້ມູນຜູ້ປົກຄອງ
# ລາຍການ Process
✅ ການລົງທະບຽນຜູ້ປົກຄອງໃໝ່
✅ ການເຊື່ອມໂຍງຜູ້ປົກຄອງກັບນັກຮຽນ
✅ ການບັນທຶກຂໍ້ມູນການຕິດຕໍ່
ການອັບເດດຂໍ້ມູນອາຊີບແລະລາຍໄດ້

# Data Store ທີ່ກ່ຽວຂ້ອງ
Parents (D25)
Student_Parent (D26)
Users (D1)
Villages (D10)
Districts (D9)
Provinces (D8)

4. ລະບົບຈັດການຊັ້ນຮຽນແລະຫ້ອງຮຽນ
# ລາຍການ Process
✅ ການສ້າງສົກຮຽນໃໝ່
✅ ການສ້າງຫ້ອງຮຽນ
✅ ການລົງທະບຽນນັກຮຽນເຂົ້າຫ້ອງ
✅ ການແຕ່ງຕັ້ງຄູປະຈຳຫ້ອງ
✅ ການກຳນົດຈຳນວນນັກຮຽນສູງສຸດ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Academic_Years (D29)
Classes (D30)
Student_Enrollments (D33)
Teachers (D27)

5. ລະບົບຈັດການການຮຽນການສອນ
# ລາຍການ Process
✅ ການເພີ່ມວິຊາຮຽນ
✅ ການມອບໝາຍວິຊາໃຫ້ຫ້ອງຮຽນ
ການມອບໝາຍຄູສອນ
✅ ການຈັດຕາຕະລາງຮຽນ
✅ ການບັນທຶກການຂາດ-ມາຮຽນ
ການບັນທຶກຄະແນນ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Subjects (D31)
Class_Subjects (D32)
Teachers (D27)
Schedules (D35)
Attendance (D34)
Student_Attendance_Summary (D38)
Examinations (D36)
Grades (D37)

6. ລະບົບຈັດການການເງິນ
# ລາຍການ Process
✅ ການກຳນົດຄ່າທຳນຽມ
✅ ການກຳນົດສ່ວນຫຼຸດ
ການສ້າງໃບແຈ້ງຊຳລະ
ການບັນທຶກການຊຳລະ
ການບັນທຶກລາຍຈ່າຍ
ການບັນທຶກລາຍຮັບອື່ນໆ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Fee_Types (D39)
Student_Fees (D40)
Discounts (D42)
Student_Discounts (D43)
Payments (D41)
Expenses (D44)
Income (D45)

7. ລະບົບລາຍງານ
# ລາຍການ Process
ການສ້າງແບບລາຍງານ
ການສ້າງລາຍງານຜົນການຮຽນ
ການສ້າງລາຍງານການເງິນ
ການສ້າງລາຍງານການຂາດ-ມາຮຽນ
ການສ້າງລາຍງານສະຖິຕິຕ່າງໆ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Report_Templates (D46)
Generated_Reports (D47)
Grades (D37)
Student_Fees (D40)
Payments (D41)
Attendance (D34)
Student_Attendance_Summary (D38)

8. ລະບົບການສື່ສານ
# ລາຍການ Process
ການສົ່ງຂໍ້ຄວາມສ່ວນຕົວ
ການປະກາດຂ່າວສານ
ການແຈ້ງເຕືອນ
ການສົ່ງການແຈ້ງເຕືອນຜົນການຮຽນ
ການຍື່ນຄຳຮ້ອງຕ່າງໆ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Messages (D48)
Announcements (D49)
Notifications (D50)
Requests (D51)
Users (D1)

9. ລະບົບຮ້ານຄ້າໂຮງຮຽນ
ລາຍການ Process
ການຈັດການສິນຄ້າ
ການເພີ່ມສິນຄ້າໃໝ່
ການຂາຍສິນຄ້າ
ການບັນທຶກການຂາຍ
ການຕິດຕາມສິນຄ້າຄົງເຫຼືອ

Data Store ທີ່ກ່ຽວຂ້ອງ
School_Store_Items (D52)
Store_Sales (D53)
Users (D1)
Students (D14)
Teachers (D27)

10. ລະບົບຫ້ອງສະໝຸດດິຈິຕອລ
# ລາຍການ Process
ການເພີ່ມຊັບພະຍາກອນດິຈິຕອລ
ການເຂົ້າເຖິງຊັບພະຍາກອນ
ການຄົ້ນຫາຊັບພະຍາກອນ
ການຕິດຕາມການໃຊ້ງານ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Digital_Library_Resources (D54)
Digital_Resource_Access (D55)
Users (D1)

11. ລະບົບກິດຈະກຳນອກຫຼັກສູດ
# ລາຍການ Process
✅ ການສ້າງກິດຈະກຳໃໝ່
✅ ການລົງທະບຽນເຂົ້າຮ່ວມກິດຈະກຳ
✅ ການຕິດຕາມຈຳນວນຜູ້ເຂົ້າຮ່ວມ
✅ ການບັນທຶກຜົນການເຂົ້າຮ່ວມ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Extracurricular_Activities (D56)
Student_Activities (D57)
Students (D14)
Academic_Years (D29)

12. ລະບົບຕັ້ງຄ່າແລະບຳລຸງຮັກສາ
# ລາຍການ Process
✅ ການຕັ້ງຄ່າລະບົບພື້ນຖານ
✅ ການຕັ້ງຄ່າຂໍ້ມູນໂຮງຮຽນ
ການສຳຮອງຂໍ້ມູນ
✅ ການຕິດຕາມການເຮັດວຽກຂອງລະບົບ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Settings (D58)
Backups (D59)
System_Logs (D60)
Users (D1)

13. ລະບົບຈັດການຂໍ້ມູນມາດຕະຖານ
# ລາຍການ Process
✅ ການຈັດການຂໍ້ມູນແຂວງ
✅ ການຈັດການຂໍ້ມູນເມືອງ
✅ ການຈັດການຂໍ້ມູນບ້ານ
✅ ການຈັດການຂໍ້ມູນສັນຊາດ
✅ ການຈັດການຂໍ້ມູນສາສະໜາ
✅ ການຈັດການຂໍ້ມູນຊົນເຜົ່າ

# Data Store ທີ່ກ່ຽວຂ້ອງ
Provinces (D8)
Districts (D9)
Villages (D10)
Nationalities (D11)
Religions (D12)
Ethnicities (D13)