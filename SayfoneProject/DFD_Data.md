**ຕາຕະລາງ External Entities (ຜູ້ໃຊ້ພາຍນອກ)**

  ---------------------------------------------------------------------------------------------
  **ລະຫັດ**   **ຊື່ External     **ຄຳອະທິບາຍ**
             Entity**         
  ---------- ---------------- -----------------------------------------------------------------
  E1         ຜູ້ບໍລິຫານໂຮງຮຽນ     ຜູ້ອຳນວຍການ, ຮອງຜູ້ອຳນວຍການ ແລະ
                              ຜູ້ບໍລິຫານລະດັບສູງທີ່ຮັບຜິດຊອບໃນການຕັດສິນໃຈແລະຄວບຄຸມການດຳເນີນງານທັງໝົດຂອງໂຮງຮຽນ

  E2         ຄູສອນ             ຄູສອນປະຈຳວິຊາ ແລະ ຄູປະຈຳຫ້ອງທີ່ຮັບຜິດຊອບການສອນ, ການໃຫ້ຄະແນນ ແລະ ຕິດຕາມນັກຮຽນ

  E3         ພະນັກງານໂຮງຮຽນ    ພະນັກງານຝ່າຍບໍລິຫານ, ພະນັກງານຝ່າຍການເງິນ, ພະນັກງານຝ່າຍທະບຽນ ແລະ
                              ພະນັກງານອື່ນໆທີ່ບໍ່ແມ່ນຄູສອນ

  E4         ນັກຮຽນ            ຜູ້ທີ່ລົງທະບຽນຮຽນໃນໂຮງຮຽນ ແລະ ເຂົ້າເຖິງຂໍ້ມູນການຮຽນຂອງຕົນເອງ

  E5         ຜູ້ປົກຄອງນັກຮຽນ      ພໍ່ແມ່ ຫຼື ຜູ້ປົກຄອງທີ່ຕິດຕາມຄວາມຄືບໜ້າການຮຽນຂອງລູກຫຼານ ແລະ ຊຳລະຄ່າຮຽນ

  E6         ລະບົບຊີວະມິຕິ        ອຸປະກອນສະແກນລາຍນິ້ວມື ຫຼື ໃບໜ້າສຳລັບການລົງທະບຽນເຂົ້າ-ອອກ ແລະ ການຢືນຢັນຕົວຕົນ

  E7         ລະບົບການຊຳລະເງິນ   ລະບົບຊຳລະເງິນພາຍນອກເຊັ່ນ BCEL ແລະ ລະບົບ QR Code ທີ່ເຊື່ອມຕໍ່ກັບລະບົບ

  E8         ຜູ້ສະໜອງສິນຄ້າ       ຜູ້ສະໜອງສິນຄ້າໃຫ້ກັບຮ້ານຄ້າໂຮງຮຽນ

  E9         ຜູ້ດູແລລະບົບ         ຜູ້ທີ່ຮັບຜິດຊອບໃນການກຳນົດຄ່າ, ບຳລຸງຮັກສາ ແລະ ຄຸ້ມຄອງລະບົບໂດຍລວມ
  ---------------------------------------------------------------------------------------------

**ຕາຕະລາງລາຍການ Datastore**

  ------------------------------------------------------------------------------------
  **ລະຫັດ**   **ຊື່ Datastore**              **ຄຳອະທິບາຍ**
  ---------- ---------------------------- --------------------------------------------
  D1         Users                        ເກັບຂໍ້ມູນຜູ້ໃຊ້ທັງໝົດ ລວມທັງຊື່ຜູ້ໃຊ້, ລະຫັດຜ່ານທີ່ເຂົ້າລະຫັດ,
                                          ສະຖານະ, ແລະ ຂໍ້ມູນພື້ນຖານອື່ນໆ

  D2         Roles                        ເກັບຂໍ້ມູນບົດບາດຕ່າງໆຂອງຜູ້ໃຊ້ໃນລະບົບ ເຊັ່ນ: ຜູ້ບໍລິຫານ,
                                          ຄູສອນ, ນັກຮຽນ, ຜູ້ປົກຄອງ

  D3         Permissions                  ເກັບຂໍ້ມູນສິດທິຕ່າງໆທີ່ສາມາດກຳນົດໃຫ້ກັບບົດບາດໃນລະບົບ

  D4         Role_Permissions             ເກັບຂໍ້ມູນການເຊື່ອມໂຍງລະຫວ່າງບົດບາດແລະສິດທິ

  D5         User_Activities              ເກັບຂໍ້ມູນກິດຈະກຳຕ່າງໆຂອງຜູ້ໃຊ້ເພື່ອການຕິດຕາມ ເຊັ່ນ:
                                          ການເຂົ້າສູ່ລະບົບ, ການແກ້ໄຂຂໍ້ມູນ

  D6         Biometric_Data               ເກັບຂໍ້ມູນຊີວະມິຕິຂອງຜູ້ໃຊ້ ເຊັ່ນ: ລາຍນິ້ວມື, ໃບໜ້າ

  D7         Biometric_Logs               ເກັບຂໍ້ມູນການບັນທຶກການໃຊ້ງານລະບົບຊີວະມິຕິ

  D8         Provinces                    ເກັບຂໍ້ມູນແຂວງທັງໝົດໃນປະເທດ

  D9         Districts                    ເກັບຂໍ້ມູນເມືອງທັງໝົດໃນປະເທດ

  D10        Villages                     ເກັບຂໍ້ມູນບ້ານທັງໝົດໃນປະເທດ

  D11        Nationalities                ເກັບຂໍ້ມູນສັນຊາດທີ່ໃຊ້ໃນລະບົບ

  D12        Religions                    ເກັບຂໍ້ມູນສາສະໜາທີ່ໃຊ້ໃນລະບົບ

  D13        Ethnicities                  ເກັບຂໍ້ມູນຊົນເຜົ່າທີ່ໃຊ້ໃນລະບົບ

  D14        Students                     ເກັບຂໍ້ມູນນັກຮຽນຄົບຖ້ວນ ລວມທັງຂໍ້ມູນສ່ວນຕົວ, ຂໍ້ມູນການສຶກສາ,
                                          ແລະ ສະຖານະ

  D15        Student_Previous_Locations   ເກັບຂໍ້ມູນທີ່ຢູ່ເກົ່າຂອງນັກຮຽນ

  D16        Student_Interests            ເກັບຂໍ້ມູນຄວາມສົນໃຈຂອງນັກຮຽນ

  D17        Student_Siblings             ເກັບຂໍ້ມູນພີ່ນ້ອງຂອງນັກຮຽນທີ່ຮຽນໃນໂຮງຮຽນ

  D18        Student_Documents            ເກັບຂໍ້ມູນເອກະສານຕ່າງໆຂອງນັກຮຽນ

  D19        Student_Health_Records       ເກັບຂໍ້ມູນສຸຂະພາບຂອງນັກຮຽນ

  D20        Student_Emergency_Contacts   ເກັບຂໍ້ມູນຜູ້ຕິດຕໍ່ກໍລະນີສຸກເສີນຂອງນັກຮຽນ

  D21        Student_Previous_Education   ເກັບຂໍ້ມູນການສຶກສາກ່ອນໜ້າຂອງນັກຮຽນ

  D22        Student_Achievements         ເກັບຂໍ້ມູນຜົນງານແລະລາງວັນຂອງນັກຮຽນ

  D23        Student_Behavior_Records     ເກັບຂໍ້ມູນພຶດຕິກຳຂອງນັກຮຽນ

  D24        Student_Special_Needs        ເກັບຂໍ້ມູນຄວາມຕ້ອງການພິເສດຂອງນັກຮຽນ

  D25        Parents                      ເກັບຂໍ້ມູນຜູ້ປົກຄອງ ລວມທັງຂໍ້ມູນສ່ວນຕົວແລະການຕິດຕໍ່

  D26        Student_Parent               ເກັບຂໍ້ມູນການເຊື່ອມໂຍງລະຫວ່າງນັກຮຽນແລະຜູ້ປົກຄອງ

  D27        Teachers                     ເກັບຂໍ້ມູນຄູສອນ ລວມທັງຂໍ້ມູນສ່ວນຕົວ, ປະຫວັດການສຶກສາ ແລະ
                                          ການຕິດຕໍ່

  D28        Teacher_Documents            ເກັບຂໍ້ມູນເອກະສານຕ່າງໆຂອງຄູສອນ

  D29        Academic_Years               ເກັບຂໍ້ມູນສົກຮຽນຕ່າງໆ

  D30        Classes                      ເກັບຂໍ້ມູນຫ້ອງຮຽນທັງໝົດໃນໂຮງຮຽນ

  D31        Subjects                     ເກັບຂໍ້ມູນວິຊາຮຽນທັງໝົດທີ່ສອນໃນໂຮງຮຽນ

  D32        Class_Subjects               ເກັບຂໍ້ມູນການເຊື່ອມໂຍງລະຫວ່າງຫ້ອງຮຽນແລະວິຊາຮຽນ

  D33        Student_Enrollments          ເກັບຂໍ້ມູນການລົງທະບຽນຂອງນັກຮຽນໃນແຕ່ລະສົກຮຽນ

  D34        Attendance                   ເກັບຂໍ້ມູນການຂາດ, ມາຮຽນຂອງນັກຮຽນ

  D35        Schedules                    ເກັບຂໍ້ມູນຕາຕະລາງສອນ

  D36        Examinations                 ເກັບຂໍ້ມູນການສອບເສັງຕ່າງໆ

  D37        Grades                       ເກັບຂໍ້ມູນຄະແນນຂອງນັກຮຽນໃນແຕ່ລະວິຊາແລະການສອບເສັງ

  D38        Student_Attendance_Summary   ເກັບຂໍ້ມູນສະຫຼຸບການຂາດ, ມາຮຽນຂອງນັກຮຽນ

  D39        Fee_Types                    ເກັບຂໍ້ມູນປະເພດຄ່າທຳນຽມຕ່າງໆ

  D40        Student_Fees                 ເກັບຂໍ້ມູນຄ່າທຳນຽມຂອງນັກຮຽນແຕ່ລະຄົນ

  D41        Payments                     ເກັບຂໍ້ມູນການຊຳລະເງິນ

  D42        Discounts                    ເກັບຂໍ້ມູນສ່ວນຫຼຸດຕ່າງໆ

  D43        Student_Discounts            ເກັບຂໍ້ມູນສ່ວນຫຼຸດທີ່ນັກຮຽນແຕ່ລະຄົນໄດ້ຮັບ

  D44        Expenses                     ເກັບຂໍ້ມູນລາຍຈ່າຍຂອງໂຮງຮຽນ

  D45        Income                       ເກັບຂໍ້ມູນລາຍຮັບຂອງໂຮງຮຽນ

  D46        Report_Templates             ເກັບຂໍ້ມູນແບບຟອມລາຍງານຕ່າງໆ

  D47        Generated_Reports            ເກັບຂໍ້ມູນລາຍງານທີ່ຖືກສ້າງຂຶ້ນ

  D48        Messages                     ເກັບຂໍ້ມູນຂໍ້ຄວາມທີ່ສົ່ງລະຫວ່າງຜູ້ໃຊ້ໃນລະບົບ

  D49        Announcements                ເກັບຂໍ້ມູນປະກາດຕ່າງໆຂອງໂຮງຮຽນ

  D50        Notifications                ເກັບຂໍ້ມູນການແຈ້ງເຕືອນຕ່າງໆໃນລະບົບ

  D51        Requests                     ເກັບຂໍ້ມູນຄຳຮ້ອງຕ່າງໆໃນລະບົບ

  D52        School_Store_Items           ເກັບຂໍ້ມູນສິນຄ້າໃນຮ້ານຄ້າຂອງໂຮງຮຽນ

  D53        Store_Sales                  ເກັບຂໍ້ມູນການຂາຍຂອງຮ້ານຄ້າ

  D54        Digital_Library_Resources    ເກັບຂໍ້ມູນຊັບພະຍາກອນດິຈິຕອລໃນຫ້ອງສະໝຸດ

  D55        Digital_Resource_Access      ເກັບຂໍ້ມູນການເຂົ້າເຖິງຊັບພະຍາກອນດິຈິຕອລ

  D56        Extracurricular_Activities   ເກັບຂໍ້ມູນກິດຈະກຳນອກຫຼັກສູດ

  D57        Student_Activities           ເກັບຂໍ້ມູນການເຂົ້າຮ່ວມກິດຈະກຳຂອງນັກຮຽນ

  D58        Settings                     ເກັບຂໍ້ມູນການຕັ້ງຄ່າລະບົບ

  D59        Backups                      ເກັບຂໍ້ມູນການສຳຮອງຂໍ້ມູນລະບົບ

  D60        System_Logs                  ເກັບຂໍ້ມູນບັນທຶກການເຮັດວຽກຂອງລະບົບ
  ------------------------------------------------------------------------------------

**ຕາຕະລາງ Feature**

  -----------------------------------------------------------------------------------------------------------
  **ລະບົບ Feature**         **ລາຍການ Process**              **Data      **ໝາຍເຫດ**
                                                           Store       
                                                           ທີ່ກ່ຽວຂ້ອງ**   
  ------------------------ ------------------------------- ----------- --------------------------------------
  ການຈັດການບັນຊີຜູ້ໃຊ້           ການລົງທະບຽນຜູ້ໃຊ້                   D1, D2, D4  ຜູ້ໃຊ້ໃໝ່ຕ້ອງໄດ້ຮັບການອະນຸມັດຈາກຜູ້ບໍລິຫານລະບົບ

                           ການເຂົ້າລະບົບ                      D1, D5      ບັນທຶກປະຫວັດການເຂົ້າລະບົບທຸກຄັ້ງ

                           ການກຳນົດສິດທິຜູ້ໃຊ້                   D2, D3, D4  ການກຳນົດບົດບາດແລະສິດທິຕ່າງໆ

                           ການລົງທະບຽນຂໍ້ມູນຊີວະມິຕິ              D1, D6      ລາຍນິ້ວມື ຫຼື ໃບໜ້າ

                           ການຕິດຕາມກິດຈະກຳຜູ້ໃຊ້               D5          ການບັນທຶກການໃຊ້ງານລະບົບ

  ການຈັດການຂໍ້ມູນນັກຮຽນ         ການລົງທະບຽນນັກຮຽນໃໝ່               D1, D14,    ລວມເຖິງເອກະສານແລະຂໍ້ມູນສຸຂະພາບ
                                                           D18, D19,   
                                                           D20         

                           ການບັນທຶກຂໍ້ມູນປະຫວັດການສຶກສາ          D21         ຂໍ້ມູນການສຶກສາກ່ອນໜ້າ

                           ການບັນທຶກຂໍ້ມູນຜົນງານແລະລາງວັນ         D22         ຜົນງານແລະລາງວັນຕ່າງໆ

                           ການບັນທຶກພຶດຕິກຳນັກຮຽນ               D23         ທັງດ້ານບວກແລະດ້ານລົບ

                           ການຈັດການນັກຮຽນທີ່ມີຄວາມຕ້ອງການພິເສດ   D24         ພ້ອມຂໍ້ແນະນຳໃນການຊ່ວຍເຫຼືອ

                           ການບັນທຶກຂໍ້ມູນທີ່ຢູ່ເກົ່າຂອງນັກຮຽນ         D15         ເພື່ອຕິດຕາມການຍ້າຍຖິ່ນຖານ

                           ການບັນທຶກຄວາມສົນໃຈຂອງນັກຮຽນ         D16         ເພື່ອຊ່ວຍໃນການພັດທະນາຫຼັກສູດທີ່ເໝາະສົມ

                           ການບັນທຶກຂໍ້ມູນພີ່ນ້ອງຂອງນັກຮຽນ          D17         ຊ່ວຍໃນການຈັດການສ່ວນຫຼຸດແລະການເຊື່ອມໂຍງຄອບຄົວ

  ການຈັດການຂໍ້ມູນຜູ້ປົກຄອງ        ການລົງທະບຽນຜູ້ປົກຄອງ                D1, D25     ລວມເຖິງຂໍ້ມູນການຕິດຕໍ່

                           ການເຊື່ອມໂຍງນັກຮຽນກັບຜູ້ປົກຄອງ         D14, D25,   ຜູ້ປົກຄອງສາມາດມີລູກຫຼາຍຄົນໃນໂຮງຮຽນ
                                                           D26         

  ການຈັດການຂໍ້ມູນຄູສອນ          ການລົງທະບຽນຄູສອນໃໝ່                D1, D27,    ລວມເຖິງເອກະສານຕ່າງໆ
                                                           D28         

                           ການມອບໝາຍວິຊາສອນໃຫ້ຄູ              D27, D31,   ຄູສາມາດສອນໄດ້ຫຼາຍວິຊາ
                                                           D32         

  ການຈັດການຊັ້ນຮຽນແລະຫ້ອງຮຽນ   ການສ້າງສົກຮຽນໃໝ່                   D29         ກຳນົດໄລຍະເວລາຂອງສົກຮຽນ

                           ການສ້າງຫ້ອງຮຽນ                    D30         ລວມເຖິງການກຳນົດລະດັບຊັ້ນ

                           ການຈັດຕາຕະລາງຮຽນ                 D31, D32,   ກຳນົດວິຊາຮຽນແລະເວລາຮຽນ
                                                           D35         

                           ການລົງທະບຽນນັກຮຽນເຂົ້າຫ້ອງ           D14, D30,   ການຈັດນັກຮຽນເຂົ້າຫ້ອງຮຽນ
                                                           D33         

  ການຈັດການການຮຽນການສອນ     ການບັນທຶກການຂາດຮຽນ                D34, D38    ການບັນທຶກປະຈຳວັນແລະສະຫຼຸບ

                           ການຈັດການວິຊາຮຽນ                  D31, D32    ການກຳນົດວິຊາຮຽນແຕ່ລະຊັ້ນ

                           ການຈັດການການສອບເສັງ               D36         ການກຳນົດໄລຍະເວລາແລະປະເພດການສອບເສັງ

                           ການບັນທຶກຄະແນນ                    D37         ການບັນທຶກຄະແນນຕາມວິຊາແລະການສອບເສັງ

  ການຈັດການການເງິນ           ການກຳນົດຄ່າທຳນຽມ                  D39         ປະເພດຄ່າທຳນຽມຕ່າງໆ

                           ການເກັບຄ່າທຳນຽມ                   D39, D40,   ການກຳນົດຄ່າທຳນຽມແລະການຊຳລະ
                                                           D41         

                           ການຈັດການສ່ວນຫຼຸດ                   D42, D43    ການກຳນົດສ່ວນຫຼຸດໃຫ້ນັກຮຽນ

                           ການບັນທຶກລາຍຈ່າຍ                   D44         ລາຍຈ່າຍຕ່າງໆຂອງໂຮງຮຽນ

                           ການບັນທຶກລາຍຮັບ                    D45         ລາຍຮັບຕ່າງໆຂອງໂຮງຮຽນ

  ການລາຍງານ                ການສ້າງແບບລາຍງານ                 D46         ການກຳນົດແບບຟອມລາຍງານຕ່າງໆ

                           ການສ້າງລາຍງານ                    D47         ການສ້າງລາຍງານຕາມຄວາມຕ້ອງການ

                           ການລາຍງານຜົນການຮຽນ               D14, D37,   ລາຍງານຄະແນນນັກຮຽນ
                                                           D47         

                           ການລາຍງານການເງິນ                 D40, D41,   ລາຍງານລາຍຮັບ-ລາຍຈ່າຍ
                                                           D44, D45,   
                                                           D47         

  ການສື່ສານ                  ການສົ່ງຂໍ້ຄວາມ                      D48         ການສື່ສານລະຫວ່າງຜູ້ໃຊ້

                           ການປະກາດຂ່າວສານ                  D49         ການປະກາດຂ່າວສານຂອງໂຮງຮຽນ

                           ການແຈ້ງເຕືອນ                      D50         ການແຈ້ງເຕືອນຕ່າງໆໃນລະບົບ

                           ການຍື່ນຄຳຮ້ອງ                      D51         ການຍື່ນຄຳຮ້ອງແລະການອະນຸມັດ

  ຮ້ານຄ້າໂຮງຮຽນ              ການຈັດການສິນຄ້າ                    D52         ການບັນທຶກສິນຄ້າໃນຮ້ານຄ້າ

                           ການຂາຍສິນຄ້າ                      D52, D53    ການບັນທຶກການຂາຍ

  ຫ້ອງສະໝຸດດິຈິຕອລ             ການຈັດການຊັບພະຍາກອນດິຈິຕອລ          D54         ການເພີ່ມຊັບພະຍາກອນດິຈິຕອລ

                           ການເຂົ້າເຖິງຊັບພະຍາກອນ              D54, D55    ການບັນທຶກການເຂົ້າໃຊ້ງານ

  ກິດຈະກຳນອກຫຼັກສູດ            ການສ້າງກິດຈະກຳ                    D56         ການເພີ່ມກິດຈະກຳນອກຫຼັກສູດ

                           ການລົງທະບຽນເຂົ້າຮ່ວມກິດຈະກຳ          D56, D57    ການລົງທະບຽນນັກຮຽນເຂົ້າຮ່ວມກິດຈະກຳ

  ການຕັ້ງຄ່າລະບົບ              ການຕັ້ງຄ່າລະບົບທົ່ວໄປ                 D58         ການຕັ້ງຄ່າຕ່າງໆຂອງລະບົບ

                           ການສຳຮອງຂໍ້ມູນ                     D59         ການສຳຮອງຂໍ້ມູນລະບົບ

                           ການບັນທຶກການເຮັດວຽກຂອງລະບົບ         D60         ການບັນທຶກການເຮັດວຽກຂອງລະບົບ

  ການຈັດການຂໍ້ມູນມາດຕະຖານ      ການຈັດການຂໍ້ມູນພື້ນຖານທາງພູມສາດ        D8, D9, D10 ແຂວງ, ເມືອງ, ບ້ານ

                           ການຈັດການຂໍ້ມູນເຊື້ອຊາດ ສັນຊາດ ແລະ     D11, D12,   ສັນຊາດ, ສາສະໜາ, ຊົນເຜົ່າ
                           ສາສະໜາ                          D13         